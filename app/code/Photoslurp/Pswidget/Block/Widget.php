<?php

namespace Photoslurp\Pswidget\Block;

class Widget extends \Magento\Framework\View\Element\Template
{
    private $_widget = null;

    private $_params = null;

    private $_containerId = null;

    private $_userName = null;

    private $categoryColFactory = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Photoslurp\Pswidget\Model\Items $widgets,
        \Magento\Framework\Locale\Resolver $resolver,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryColFactory,
        array $data = []
    ) {
    
        parent::__construct($context);

        if (!isset($data['widget_id'])) {
            return;
        }

        $this->_widget = $widgets->load($data['widget_id']);

        if (!$this->_widget->getWidgetEnable()) {
            return;
        }

        $this->categoryColFactory = $categoryColFactory;

        $params = $this->_widget->getData();
        $params['lang'] = $resolver->getLocale().'_'. $this->_storeManager->getStore()->getId();

        if ($product = $registry->registry('current_product')) {
            $params['product_id'] = $this->prepareProductIdParam($product);
            if($params['product_type']){
                $params['product_type'] = $this->getProductProductType($product);
            }
        } elseif ($category = $registry->registry('current_category')) {
            if($params['product_type']) {
                $params['product_type'] = array($this->getCategoryPath($category));
            }
        }

        if ($additionalParams = json_decode('{'.$this->_widget->getAdditionalParams().'}', true)) {
            $params = array_merge($params, $additionalParams);
        }

        $this->_params = $params;
        $this->_containerId = 'ps_container_'.$this->_widget->getId();
        $this->_userName = $this->_widget->getUserName();

        $this->setTemplate('Photoslurp_Pswidget::widget.phtml');
    }

    private function prepareProductIdParam($product)
    {
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $children = $product->getTypeInstance()->getUsedProducts($product);
            $skus[] = $product->getSku();
            foreach ($children as $child) {
                $skus[] = $child->getSku();
            }
            $productIdParam = $skus;
        } else {
            $productIdParam = $product->getSku();
        }
        return $productIdParam;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function getContainerId()
    {
        return $this->_containerId;
    }

    public function getUserName()
    {
        return $this->_userName;
    }

    public function getWidget()
    {
        return $this->_widget;
    }

    public function getJson()
    {
        $data = $this->_params;

        if (isset($data['lookbook_product_types'])) {
            $types = json_decode($data['lookbook_product_types']);
            $lbCategories = $this->categoryColFactory->create()->addNameToResult()
                ->addAttributeToFilter('entity_id',$types);
            $lbProductTypes = array();
            foreach ($lbCategories as $lbCategory){
                $lbProductTypes[] = $this->getCategoryPath($lbCategory);
            }
            $data['product_type'] = $lbProductTypes;
        }

        unset($data['id']);
        unset($data['widget_id']);
        unset($data['widget_enable']);
        unset($data['position']);
        unset($data['position_category']);
        unset($data['user_name']);
        unset($data['id_widget']);
        unset($data['css']);
        unset($data['page_type']);
        unset($data['website']);
        unset($data['lookbook_product_types']);
        if($data['product_type'] == 0) unset($data['product_type']);
        unset($data['category']);
        unset($data['display_in_categories']);

        $parameters = [];
        foreach ($data as $path => $value) {
            if (($value !== null)) {
                if (strpos($path, 'style_')=== 0) {
                    $temp = &$parameters;
                    foreach (explode('_', $path) as $key) {
                        $temp = &$temp[$key];
                    }

                    $temp = $value;
                    unset($temp);
                } else {
                    $jsName = preg_replace_callback(
                        '/_([^_])/',
                        function (array $m) {
                            return ucfirst($m[1]);
                        },
                        $path
                    );
                    $parameters[$jsName] = $value;
                }
            }
        }

        return json_encode($parameters);
    }

    public function getCssUrl()
    {
        return $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'photoslurp/'.$this->_widget->getWidgetId().'.css';
    }

    public function getProductProductType($product){
        $categoryIds = $product->getCategoryIds();
        $productTypes = array();
        if($categoryIds){
            $categoryCollection = $this->categoryColFactory->create()
                ->addAttributeToFilter('entity_id',$categoryIds);
            foreach ($categoryCollection as $category){
                $productTypes[] = $this->getCategoryPath($category);
            }
        }
        return $productTypes;
    }

    public function getCategoryPath($category){
        $pathArray = explode('/',$category->getPath());
        unset($pathArray[0]);
        $pathCategories = $this->categoryColFactory->create()->addNameToResult()
            ->addAttributeToFilter('entity_id',$pathArray);
        $names = array();
        foreach ($pathCategories as $pathCategory){
            $names[] = $pathCategory->getName();
        }
        return implode('>',$names);
    }
}
