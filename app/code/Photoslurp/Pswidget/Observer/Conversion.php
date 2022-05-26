<?php

namespace Photoslurp\Pswidget\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;

class Conversion implements ObserverInterface
{

    protected $_storeManager;

    protected $_scopeConfig;

    protected $_cookieManager;

    protected $_orderFactory;

    protected $_logger;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
    
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_cookieManager = $cookieManager;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $logger;
    }

    public function execute(Observer $observer)
    {
        if ($this->_scopeConfig->getValue('tracking_section/general/tracking_enable')) {
            $orderIds = $observer->getEvent()->getOrderIds();
            if (count($orderIds)) {
                $orderId = $orderIds[0];
                $order = $this->_orderFactory->create()->load($orderId);
                $items = $order->getAllVisibleItems();
                $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
                $campaignId = $this->_scopeConfig->getValue('tracking_section/general/campaign_id');

                $data = [
                    'event' => 'widget_converted',
                    'visitor_id' => $this->getVisitorId(),
                    'order_id' => $order->getIncrementId(),
                    'referrer' => $this->_storeManager->getStore()->getBaseUrl()
                ];
                if ($campaignId) {
                    $data['album_id'] = $campaignId;
                }

                foreach ($items as $item) {
                    $data['products'][$item->getProduct()->getSku()] = ['count' => $item->getQtyOrdered(), 'price' => $item->getPrice(), 'currency' => $currencyCode];
                }

                $uri = 'http://api.photoslurp.com/v3/widgets/record/';
                $client = new \Zend_Http_Client();
                $client->setUri($uri);
                $client->setRawData(json_encode($data), 'application/json');
                try {
                    $response = $client->request(\Zend_Http_Client::POST);
                    $this->log('Response: '. $response);
                }
                catch (\Zend_Http_Client_Exception $e) {
                    unset($e);
                }
            }
        }
    }

    protected function getVisitorId(){
        $this->log('Getting "ps_analytics" from cookies.');
        $visitorId = $this->_cookieManager->getCookie('ps_analytics');
        if(!$visitorId){
            $this->log('"ps_analytics" cookie does not exist. Generating "visitor_id"...');
            $visitorIdLength = 20;
            $visitorId = '';
            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            for ($i=0; $i<$visitorIdLength; $i++){
                $visitorId .= $alphabet[random_int(0, 300)%strlen($alphabet)];
            }
        }
        $this->log('"visitor_id" : '.$visitorId );
        return $visitorId;
    }

    protected function log($message){
        if ($this->_scopeConfig->getValue('tracking_section/general/log_enable')) {
            $this->_logger->debug($message);
        }
    }
}
