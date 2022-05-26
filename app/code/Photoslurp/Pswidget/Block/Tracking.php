<?php

namespace Photoslurp\Pswidget\Block;

class Tracking extends \Magento\Framework\View\Element\Template
{

    private $_checkoutSession = null;

    private $_orderItems = null;

    private $_currencyCode = null;

    private $_configTrackingEnabled = null;

    private $_orderId = null;

    private $_campaignId = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
    
        parent::__construct($context);

        $this->_currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $this->_checkoutSession = $checkoutSession;

        $this->_orderItems = $checkoutSession->getLastRealOrder()->getAllVisibleItems();

        $this->_orderId = $checkoutSession->getLastRealOrder()->getIncrementId();

        $this->_configTrackingEnabled = $this->_scopeConfig->getValue('tracking_section/general/tracking_enable');

        $this->_campaignId = $this->_scopeConfig->getValue('tracking_section/general/campaign_id');
    }

    public function getOrderItems()
    {
        return $this->_orderItems;
    }

    public function getCurrencyCode()
    {
        return $this->_currencyCode;
    }

    public function getOrderId()
    {
        return $this->_orderId;
    }

    public function getIsTrackingEnabled()
    {
        return $this->_configTrackingEnabled;
    }

    public function getCampaignId()
    {
        return $this->_campaignId;
    }
}
