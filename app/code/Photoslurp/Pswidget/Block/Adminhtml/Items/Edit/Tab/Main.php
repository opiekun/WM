<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Photoslurp\Pswidget\Block\Adminhtml\Items\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;



class Main extends Generic implements TabInterface
{
    private $_stores = null;

    private $_storesCount = null;

    private $_categoryColFactory = null;

    private $_fieldFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        array $data = []
    )
    {
        parent::__construct($context,$registry,$formFactory);
        $this->_stores = $this->_storeManager->getStores($withDefault = false);
        $this->_storesCount = count($this->_stores);
        $this->_categoryFactory = $categoryFactory;
        $this->_fieldFactory = $fieldFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_photoslurp_pswidget_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'widget_enable',
            'select',
            ['name' => 'widget_enable', 'label' => __('Enable'), 'title' => __('Enable'), 'required' => false,
                'options' => ['0'=>'No','1' => 'Yes'],
            ]
        );
        $fieldset->addField(
            'user_name',
            'text',
            ['name' => 'user_name', 'label' => __('User Name'), 'title' => __('User Name'), 'required' => true,
                'note' => __(
                    'Your Photoslurp user name.'
                ),
            ]
        );
        $fieldset->addField(
            'widget_id',
            'text',
            ['name' => 'widget_id', 'label' => __('Widget Id'), 'title' => __('Widget Id'), 'required' => true,
                'note' => __(
                    'A unique identifier for each of the widgets you use on your website.'
                ),
            ]
        );

        $fieldset->addField(
            'page_type',
            'select',
            ['name' => 'page_type', 'label' => __('Page Type'), 'title' => __('Page Type'), 'required' => true,
                'options' => ['home'=>'Home','product' => 'Product','lookbook' => 'Lookbook','category' => 'Category'],
                'note' => __(
                    'Choose the type of page you want to embed this widget in.'
                ),
            ]
        );

        foreach ($this->_storeManager->getWebsites() as $site){
            $websites[] = ['value'=>$site->getId(), 'label'=>$site->getName()];
        }

        $fieldset->addField(
            'website',
            'multiselect',
            [
                'name' => 'website',
                'label' => __('Website'),
                'values' => $websites,
                'required' => true,
            ]
        );

        $fieldset->addField(
            'display_in_categories',
            'select',
            [
                'name' => 'display_in_categories',
                'label' => __('Display In Categories'),
                'values' => ['all'=>'All categories', 'selected'=>'Only in selected categories'],
                'note' => __(
                    'Choose whether you want this widget to be displayed in all category pages or 
                    in a custom selection of category pages.'
                ),
                'use_default' => true,
            ]
        );

        $fieldset->addField(
            'category',
            'multiselect',
            [
                'name' => 'category',
                'label' => __('Selected Categories'),
                'values' => $this->getValueArrayCategories(),
                'note' => __(
                    'To select multiple categories hold down the Ctrl-key on Windows or the Command-key 
                    on Mac while clicking on the category names. To select a range: Click the first category, 
                    then hold down the Shift-key and then click the last category in the range.'
                ),
                'required' => true
            ]
        );

        $fieldset->addField(
            'position',
            'select',
            ['name' => 'position', 'label' => __('Position'), 'title' => __('Position'),
                'options' => [
                    'content'=>'Product Content Bottom',
                    'product.info.media'=>'After Product Info Media',
                    'product.info.details'=>'After Product Info Details'
                ],
                'note' => __(
                    'Position on product page.'
                ),
            ]
        );

        $fieldset->addField(
            'position_category',
            'select',
            ['name' => 'position_category', 'label' => __('Position'), 'title' => __('Position'),
                'options' => [
                    'bottom'=>'Bottom',
                    'top'=>'Top',
                ],
                'note' => __(
                    'Position on category page.'
                ),
            ]
        );

        $fieldset->addField(
            'product_type',
            'select',
            ['name' => 'product_type', 'label' => __('Only Show Media From Product Category'),
                'title' => __('Only Show Media From Product Category'),
                'options' => ['0'=>'No','1' => 'Yes'],
                'note' => __(
                    'When no media is available for a specific product,
                     show only photos related to products in the same product category.'
                ),
                'use_default'=>true
            ]
        );

        $fieldset->addField(
            'lookbook_product_types',
            'multiselect',
            ['name' => 'lookbook_product_types', 'label' => __('Only Show Media From Product Categories'),
                'title' => __('Only Show Media From Product Categories'), 'required' => true,
                'values' => $this->getValueArrayCategories(),
                'note' => __('Show media related to products in given product categories.')
            ]
        );

        // define field dependencies
        $blockDependence = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence'
        );

        $blockDependence
            ->addFieldMap("item_page_type", 'page_type')
            ->addFieldMap("item_position", 'position')
            ->addFieldDependence(
                'position',
                'page_type',
                'product'
            );

        $blockDependence
            ->addFieldMap("item_page_type", 'page_type')
            ->addFieldMap("item_position_category", 'position_category')
            ->addFieldDependence(
                'position_category',
                'page_type',
                'category'
            );

        $refField = $this->_fieldFactory->create(
            ['fieldData' => ['value' => 'product,lookbook,category', 'separator' => ','], 'fieldPrefix' => '']
        );

        $blockDependence
            ->addFieldMap("item_product_type", 'product_type')
            ->addFieldDependence('product_type', 'page_type', $refField);

        $blockDependence
            ->addFieldMap("item_lookbook_product_types", 'lookbook_product_types')
            ->addFieldDependence('lookbook_product_types', 'page_type', 'lookbook')
            ->addFieldDependence('lookbook_product_types', 'product_type', '1');

        $refField = $this->_fieldFactory->create(
            ['fieldData' => ['value' => 'product,category', 'separator' => ','], 'fieldPrefix' => '']
        );

        $blockDependence
            ->addFieldMap("item_website", 'website')
            ->addFieldDependence('website', 'page_type', $refField);

        $blockDependence
            ->addFieldMap("item_category", 'category')
            ->addFieldMap("item_display_in_categories", 'display_in_categories')
            ->addFieldDependence('category', 'display_in_categories', 'selected')
            ->addFieldDependence('category', 'page_type', 'category')
            ->addFieldDependence('display_in_categories', 'page_type', 'category');

        $this->setChild(
            'form_after',
            $blockDependence
        );

        $_data = $model->getData();

        if (!empty($_data)) {
            if(isset($_data['lookbook_product_types'])){
                $_data['lookbook_product_types'] = json_decode($_data['lookbook_product_types']);
            }
            if(isset($_data['category'])){
                $_data['category'] = json_decode($_data['category']);
            }
            if(isset($_data['website'])){
                $_data['website'] = json_decode($_data['website']);
            }
            $form->setValues($_data);
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected $lookBookCategories;

    protected function getValueArrayCategories()
    {
        $categories =  $this->_categoryFactory->create()->getCategories(1);
        self::getCategories($categories);
        return $this->lookBookCategories;
    }

    protected function getCategories($categories)
    {
        foreach($categories as $category) {
            $labelPrefix = "";
            for($i=0; $i<$category->getLevel(); $i++){
                $labelPrefix .= '..';
            }
            $this->lookBookCategories[] = array('value'=>$category->getId(),'label'=>$labelPrefix.$category->getName());
            if($category->hasChildren()) {
                $children = $this->_categoryFactory->create()->getCategories($category->getId());
                $this->getCategories($children);
            }
        }
    }
}
