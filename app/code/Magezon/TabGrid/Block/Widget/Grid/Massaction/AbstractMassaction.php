<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_TabGrid
 * @copyright Copyright (C) 2017 Magezon (https://magezon.com)
 */

namespace Magezon\TabGrid\Block\Widget\Grid\Massaction;

use Magento\Framework\View\Element\Template;

abstract class AbstractMassaction extends \Magezon\TabGrid\Block\Widget
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * Massaction items
     *
     * @var array
     */
    protected $_items = [];

    /**
     * @var string
     */
    protected $_template = 'Magezon_TabGrid::widget/grid/massaction.phtml';

    /**
     * @param \Magezon\TabGrid\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magezon\TabGrid\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setErrorText($this->escapeJsQuote(__('Please select items.')));

        if (null !== $this->getOptions()) {
            foreach ($this->getOptions() as $optionId => $option) {
                $this->addItem($optionId, $option);
            }
            $this->unsetData('options');
        }
    }

    /**
     * Add new massaction item
     *
     * Item array to be passed in looks like:
     * $item = array(
     *      'label'    => string,
     *      'complete' => string, // Only for ajax enabled grid (optional)
     *      'url'      => string,
     *      'confirm'  => string, // text of confirmation of this action (optional)
     *      'additional' => string // (optional)
     * );
     *
     * @param string $itemId
     * @param array|\Magento\Framework\DataObject $item
     * @return $this
     */
    public function addItem($itemId, $item)
    {
        if (is_array($item)) {
            $item = new \Magento\Framework\DataObject($item);
        }

        if ($item instanceof \Magento\Framework\DataObject) {
            $item->setId($itemId);
            $item->setUrl($this->getUrl($item->getUrl()));
            $this->_items[$itemId] = $item;
        }

        return $this;
    }

    /**
     * Retrieve massaction item with id $itemId
     *
     * @param string $itemId
     * @return \Magezon\TabGrid\Block\Widget\Grid\Massaction\Item|null
     */
    public function getItem($itemId)
    {
        if (isset($this->_items[$itemId])) {
            return $this->_items[$itemId];
        }

        return null;
    }

    /**
     * Retrieve massaction items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Retrieve massaction items JSON
     *
     * @return string
     */
    public function getItemsJson()
    {
        $result = [];
        foreach ($this->getItems() as $itemId => $item) {
            $result[$itemId] = $item->toArray();
        }

        return $this->_jsonEncoder->encode($result);
    }

    /**
     * Retrieve massaction items count
     *
     * @return integer
     */
    public function getCount()
    {
        return sizeof($this->_items);
    }

    /**
     * Checks are massactions available
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->getCount() > 0 && $this->getMassactionIdField();
    }

    /**
     * Retrieve global form field name for all massaction items
     *
     * @return string
     */
    public function getFormFieldName()
    {
        return $this->getData('form_field_name') ? $this->getData('form_field_name') : 'massaction';
    }

    /**
     * Retrieve form field name for internal use. Based on $this->getFormFieldName()
     *
     * @return string
     */
    public function getFormFieldNameInternal()
    {
        return 'internal_' . $this->getFormFieldName();
    }

    /**
     * Retrieve massaction block js object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Retrieve grid block js object name
     *
     * @return string
     */
    public function getGridJsObjectName()
    {
        return $this->getParentBlock()->getJsObjectName();
    }

    /**
     * Retrieve JSON string of selected checkboxes
     *
     * @return string
     */
    public function getSelectedJson()
    {
        if ($selected = $this->getRequest()->getParam($this->getFormFieldNameInternal())) {
            $selected = explode(',', $selected);
            return join(',', $selected);
        } else {
            return '';
        }
    }

    /**
     * Retrieve array of selected checkboxes
     *
     * @return string[]
     */
    public function getSelected()
    {
        if ($selected = $this->getRequest()->getParam($this->getFormFieldNameInternal())) {
            $selected = explode(',', $selected);
            return $selected;
        } else {
            return [];
        }
    }

    /**
     * Retrieve apply button html
     *
     * @return string
     */
    public function getApplyButtonHtml()
    {
        return $this->getButtonHtml(__('Submit'), $this->getJsObjectName() . ".apply()");
    }

    /**
     * @return string
     */
    public function getJavaScript()
    {
        return " {$this->getJsObjectName()} = new varienGridMassaction('{$this->getHtmlId()}', " .
            "{$this->getGridJsObjectName()}, '{$this->getSelectedJson()}'" .
            ", '{$this->getFormFieldNameInternal()}', '{$this->getFormFieldName()}');" .
            "{$this->getJsObjectName()}.setItems({$this->getItemsJson()}); " .
            "{$this->getJsObjectName()}.setGridIds('{$this->getGridIdsJson()}');" .
            ($this->getUseAjax() ? "{$this->getJsObjectName()}.setUseAjax(true);" : '') .
            ($this->getUseSelectAll() ? "{$this->getJsObjectName()}.setUseSelectAll(true);" : '') .
            "{$this->getJsObjectName()}.errorText = '{$this->getErrorText()}';" . "\n" .
            "window.{$this->getJsObjectName()} = {$this->getJsObjectName()};";
    }

    /**
     * @return string
     */
    public function getGridIdsJson()
    {
        if (!$this->getUseSelectAll()) {
            return '';
        }

        /** @var \Magento\Framework\Data\Collection $allIdsCollection */
        $allIdsCollection = clone $this->getParentBlock()->getCollection();
        $gridIds = $allIdsCollection->clear()->setPageSize(0)->getAllIds();

        if (!empty($gridIds)) {
            return join(",", $gridIds);
        }
        return '';
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getParentBlock()->getHtmlId() . '_massaction';
    }

    /**
     * Remove existing massaction item by its id
     *
     * @param string $itemId
     * @return $this
     */
    public function removeItem($itemId)
    {
        if (isset($this->_items[$itemId])) {
            unset($this->_items[$itemId]);
        }

        return $this;
    }

    /**
     * Retrieve select all functionality flag check
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseSelectAll()
    {
        return $this->_getData('use_select_all') === null || $this->_getData('use_select_all');
    }

    /**
     * Retrieve select all functionality flag check
     *
     * @param boolean $flag
     * @return $this
     */
    public function setUseSelectAll($flag)
    {
        $this->setData('use_select_all', (bool)$flag);
        return $this;
    }

    /**
     * Prepare grid massaction column
     *
     * @return $this
     */
    public function prepareMassactionColumn()
    {
        $columnId = 'massaction';
        $massactionColumn = $this->getLayout()->createBlock(
            'Magezon\TabGrid\Block\Widget\Grid\Column'
        )->setData(
            [
                'index' => $this->getMassactionIdField(),
                'filter_index' => $this->getMassactionIdFilter(),
                'type' => 'massaction',
                'name' => $this->getFormFieldName(),
                'is_system' => true,
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select',
            ]
        );

        if ($this->getNoFilterMassactionColumn()) {
            $massactionColumn->setData('filter', false);
        }

        $gridBlock = $this->getParentBlock();
        $massactionColumn->setSelected($this->getSelected())->setGrid($gridBlock)->setId($columnId);

        /** @var $columnSetBlock \Magezon\TabGrid\Block\Widget\Grid\ColumnSet */
        $columnSetBlock = $gridBlock->getColumnSet();
        $childNames = $columnSetBlock->getChildNames();
        $siblingElement = count($childNames) ? current($childNames) : 0;
        $columnSetBlock->insert($massactionColumn, $siblingElement, false, $columnId);
        return $this;
    }
}
