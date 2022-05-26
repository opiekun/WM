<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Photoslurp\Pswidget\Block\Adminhtml\Items\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;



class Style extends Generic implements TabInterface
{
    private $_stores = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        parent::__construct($context,$registry,$formFactory);
        $this->_stores = $this->_storeManager->getStores($withDefault = false);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Style');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Style');
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
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Style')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

//        $fieldset->addField(
//            'style_custom_enable',
//            'select',
//            ['name' => 'style_custom_enable', 'label' => __('Enable Custom CSS'), 'title' => __('Enable Custom CSS'),
//                'options' => ['0'=>'No','1' => 'Yes'],
//            ]
//        );

        $fieldset->addField(
            'css',
            'textarea',
            ['name' => 'css', 'label' => __('Enter your CSS here'), 'title' => __('Custom CSS'),
                'note' => __(
                    'Enter your custom CSS code here. Only requires dot notation with !important flag. Eg: .galleria-info-product-description { margin-top:50px !important }.'
                ),
                'use_default'=>true
            ]
        );

        $_data = $model->getData();

        if (!empty($_data)) {
            $form->setValues($_data);
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
