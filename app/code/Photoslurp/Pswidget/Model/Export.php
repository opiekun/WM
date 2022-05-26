<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

namespace Photoslurp\Pswidget\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class Export extends \Magento\Framework\Model\AbstractModel
{
    const XML_PATH_PRODUCT_URL_SUFFIX = 'catalog/seo/product_url_suffix';

    private $_delimiter = '|';

    private $_defaultFileName = 'photoslurp_export.csv';

    private $_currencyModel = null;

    private $_storeManager  = null;

    private $_scopeConfig = null;

    private $_iterator = null;

    private $_productCollection = null;

    private $_directory = null;

    private $_stores = null;

    private $_currecies = null;

    private $_baseCurrency = null;

    private $_urlMedia = null;

    private $_exportMode = null;

    private $_configExportPath = null;

    private $_directoryList = null;

    private $_fileNameToWrite = null;

    private $_productUrlSuffix = [];

    private $_objectManager = null;

    protected $_categoryNames = [];

    protected $_categoryPaths = [];

    protected $_productMetadata = null;

    protected $_filesystem = null;

    protected $_categoryColFactory = null;

    protected $_splitFeed = false;

    public function __construct(
        \Magento\Directory\Model\Currency $currencyModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $iterator,
        \Magento\Directory\Helper\Data $directory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryColFactory
    ) {
        $this->_currencyModel = $currencyModel;
        $this->_storeManager  = $storeManager;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_scopeConfig = $scopeConfig;
        $this->_productCollection  = $productCollection;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_iterator  = $iterator;
        $this->_directory  = $directory;
        $this->_productMetadata = $productMetadata;
        $this->_filesystem = $filesystem;
        $this->_categoryColFactory = $categoryColFactory;
        $this->_splitFeed = $this->_scopeConfig->getValue('export_section/general/feeds');

        $stores = $this->_storeManager->getStores($withDefault = false);
        if ($configStoresAllowed = $this->_scopeConfig->getValue('export_section/general/stores')) {
            $storesAllowed = explode(',', $configStoresAllowed);
            foreach ($stores as $store) {
                if (in_array($store->getId(), $storesAllowed)) {
                    $this->_stores[] = $store;
                }
            }
        } else {
            $this->_stores = $stores;
        }

        $this->_currecies = $this->_currencyModel->getConfigAllowCurrencies();
        $this->_baseCurrency = $this->_storeManager->getStore()->getBaseCurrencyCode();
        $this->_urlMedia = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->_exportMode = $this->_scopeConfig->getValue('export_section/general/mode');
        $this->_configExportPath = $this->_scopeConfig->getValue('export_section/general/path');
        $this->_directoryList = $directoryList;

        if ($this->_configExportPath) {
            $this->_fileNameToWrite = $this->_configExportPath;
            $dirname = dirname($this->_fileNameToWrite);
            if (!is_dir($dirname)) {
                mkdir($dirname, 0755, true);
            }
        } else {
            $mediapath = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $this->_fileNameToWrite = $mediapath->getAbsolutePath().$this->_defaultFileName;
        }

        foreach ($this->_stores as $store) {
            $categoryCollection = $this->_categoryColFactory->create()->addNameToResult();
            $categoryCollection->setStoreId($store->getId());
            foreach ($categoryCollection as $category){
                $this->_categoryNames[$store->getId()][$category->getId()] = $category->getName();
                $this->_categoryPaths[$store->getId()][$category->getId()] = $category->getPath();
            }
            foreach ($this->_categoryPaths[$store->getId()] as $id => $path){
                $pathArray = explode('/',$path);
                unset($pathArray[0]);
                $this->_categoryPaths[$store->getId()][$id] = implode('>',
                    array_intersect_key($this->_categoryNames[$store->getId()], array_flip($pathArray)));
            }
        }
    }

    public function photoslurpExport($schedule=null){

        if($schedule && !$this->_scopeConfig->getValue('export_section/general/cron')) return;

        if ($this->_splitFeed) {
            $feedsFile = preg_replace('/\w+.csv/', 'feeds.txt', $this->_fileNameToWrite);
            $fp = fopen($feedsFile, 'w');
            foreach ($this->_stores as $store) {
                $this->export($store);
                fwrite($fp, $this->getDownloadLink($store->getId()) . PHP_EOL);
            }
            fclose($fp);
        }else{
            $this->export();
        }
    }

    public function exportCron()
    {
        if ($this->_scopeConfig->getValue('export_section/general/cron')) {
            $this->export();
        }
    }

    protected function setHeader($store, &$headerCsv){
        $storeId = $store->getId();
        $localeCode = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $suffix = sprintf('_%s_%s', $localeCode, $storeId);
        $headerCsv[] = 'title'.$suffix;
        $headerCsv[] = 'description'.$suffix;
        $headerCsv[] = 'url'.$suffix;
        $headerCsv[]='product_types'.$suffix;
    }

    public function export($store=null)
    {
        $headerCsv = ['sku'];
        foreach ($this->_currecies as $currecy) {
            $headerCsv[] = 'price_'.$currecy;
        }
        $headerCsv[]='in_stock';
        $headerCsv[]='image_url';

        if($store){
            $this->setHeader($store, $headerCsv);
            $collection = $this->getPreparedCollection([$store]);
        } else {
            foreach ($this->_stores as $s) {
                $this->setHeader($s, $headerCsv);
            }
            $collection = $this->getPreparedCollection($this->_stores);
        }

        $headerCsv[]='google_category';
        $headerCsv[]='gender';

        if($store){
            $fileName = preg_replace('/\w+.csv/', $store->getId().'_$0', $this->_fileNameToWrite);
        } else {
            $fileName = $this->_fileNameToWrite;
        }

        $fp = fopen($fileName, 'w');
        fwrite($fp, '# exported at ' . date('d-m-Y').' 3.1.4 '.$this->_productMetadata->getVersion() . PHP_EOL);
        /**
         * Ecommerce121 comment: we fix this to avoid this error: Passing glue string after array is deprecated
         */
        fwrite($fp, implode($this->_delimiter, $headerCsv) . PHP_EOL);

        $this->_iterator->walk(
            $collection->getSelect(),
            [[$this, 'walkCallback']],
            ['stores'=> ($store) ? [$store] : $this->_stores,'fp' => $fp]
        );

        fclose($fp);
    }

    public function walkCallback($data)
    {
        $fp         = $data['fp'];
        $row        = $data['row'];
        $insertData = [$row['sku']];
        $stores     = $data['stores'];

        foreach ($this->_currecies as $currency) {
            $insertData[] = $this->_directory->currencyConvert($row['price'], $this->_baseCurrency, $currency);
        }

        $insertData[] = ($row['is_in_stock'])?'in stock':'out of stock';

        $insertData[] = $row['image']?$this->_urlMedia .'catalog/product'.$row['image'] : '';

        $product = $this->_objectManager->get('Magento\Catalog\Model\Product');
        $product->setData($row);
        $productStores = $product->getStoreIds();

        $pattern = '/"/';
        $replacement = '$0"';

        foreach ($stores as $store) {
            $id = $store->getId();
            $isProductInStore = in_array($id, $productStores);
            $insertData[] = $isProductInStore ? sprintf('"%s"', preg_replace($pattern, $replacement, $row['name_'.$id])):'';
            $insertData[] = $isProductInStore ? sprintf('"%s"', preg_replace($pattern, $replacement, $row['description_'.$id])):'';
            $insertData[] = $isProductInStore ? $store->getBaseUrl().$row['url_key_'.$id].$this->getProductUrlSuffix($id).'?___store='.$store->getCode():'';

            $categoryPaths = [];
            $categoryIds = $product->getCategoryIds();
            foreach ($categoryIds as $id){
                $categoryPaths[$store->getId()][] = $this->_categoryPaths[$store->getId()][$id];
            }
            $insertData['product_types_'.$store->getId()] = array_key_exists($store->getId(), $categoryPaths) ? implode(',',$categoryPaths[$store->getId()]) : '';
        }

        $insertData['google_category'] = '';
        $insertData['gender'] = $product->getGender();

        /**
         * Ecommerce121 comment: we fix this to avoid this error: Passing glue string after array is deprecated
         */
        fwrite($fp, implode($this->_delimiter, $insertData) . PHP_EOL);
    }


    private function getPreparedCollection($stores)
    {
        $collection = $this->_productCollectionFactory->create();

        $collection
            ->addAttributeToFilter('visibility', ["neq"=>1])
            ->addAttributeToSelect(['price','image'], 'left');

        $collection->addWebsiteFilter($this->getWebsitesFilter());

        $collection->joinField(
            'is_in_stock',
            'cataloginventory_stock_item',
            'is_in_stock',
            'product_id=entity_id',
            null,
            'left'
        );

        foreach ($stores as $store) {
            $storeId = $store->getId();
            $collection->joinAttribute(
                'name_'.$storeId,
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $storeId
            );
            $collection->joinAttribute(
                'description_'.$storeId,
                'catalog_product/description',
                'entity_id',
                null,
                'left',
                $storeId
            );
            $collection->joinAttribute(
                'url_key_'.$storeId,
                'catalog_product/url_key',
                'entity_id',
                null,
                'left',
                $storeId
            );
        }
        return $collection;
    }

    private function getWebsitesFilter()
    {
        $websites = [];
        foreach ($this->_stores as $store) {
            $websites[] = $store->getWebsiteId();
        }
        $websites = array_unique($websites);
        return $websites;
    }

    private function getProductUrlSuffix($storeId = null)
    {
        if (!isset($this->_productUrlSuffix[$storeId])) {
            $this->_productUrlSuffix[$storeId] = $this->_scopeConfig->getValue(
                self::XML_PATH_PRODUCT_URL_SUFFIX,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->_productUrlSuffix[$storeId];
    }

    protected function getDownloadLink($storeId)
    {
        $configExportPath = $this->_scopeConfig->getValue('export_section/general/path');
        if ($configExportPath) {
            $configExportPath  = preg_replace('/\w+.csv/', $storeId.'_$0', $configExportPath);
            $downloadLink = $this->_storeManager->getStore()->getBaseUrl().$configExportPath;
        } else {
                $downloadLink = $this->_storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $storeId .'_'. $this->_defaultFileName;
        }
        return $downloadLink;
    }
}
