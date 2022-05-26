<?php

namespace Photoslurp\Pswidget\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;

class ProductPageWidget implements ObserverInterface
{
    protected $_storeManager;

    protected $_registry;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
    }

    public function execute(Observer $observer)
    {
        if ($observer->getFullActionName() == 'catalog_product_view') {
            $this->createWidget('product', $observer);
        } elseif ($observer->getFullActionName() == 'catalog_category_view') {
            $this->createWidget('category', $observer);
        }
    }

    protected function createWidget($type, $observer){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('\Photoslurp\Pswidget\Model\Items');
        $collection = $model->getCollection();
        $collection->addFilter('page_type', $type);
        if (count($this->_storeManager->getWebsites())>1) {
            $collection
                ->addFilter('website', ['like' => '%"'.$this->_storeManager->getStore()->getWebsiteId().'"%'], 'public');
        }

        if($type == 'category'){
            $currentCategory = $this->_registry->registry('current_category');
            $collection->addFieldToFilter(
                'category',
                [
                    ['like' => '%"'.$currentCategory->getId().'"%'],
                    ['null' => true]
                ]
            );
        }

        $widget = $collection->getFirstItem();

        $widgetId = $widget->getId();

        if ($widgetId) {
            $layout = $observer->getLayout();

            $block = $layout->createBlock(
                'Photoslurp\Pswidget\Block\Widget',
                'pswidget_catalog',
                ['data' => ['widget_id' => $widgetId,]]
            );

            $params = $block->getParams();

            $layout->setChild('content', 'pswidget_catalog', 'pswidget_catalog');

            if($type == 'category'){
                $after = true;
                if($params['position_category'] == 'top'){
                    $after = false;
                }
                $layout->reorderChild('content', 'pswidget_catalog', 'category.products', $after);
            }else{
                $layout->reorderChild('content', 'pswidget_catalog', $params['position'], $after = true);
            }

        }
    }
}
