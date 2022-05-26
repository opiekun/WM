<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */
namespace Photoslurp\Pswidget\Block\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;

class Export extends \Magento\Backend\Block\Template
{
    private $_directoryList = null;

    private $_defaultFileName = 'photoslurp_export.csv';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        parent::__construct($context);
        $this->_directoryList = $directoryList;
    }

    public function getDownloadLink()
    {
        $downloadLink = '';
        $configExportPath = $this->_scopeConfig->getValue('export_section/general/path');

        if($this->_scopeConfig->getValue('export_section/general/feeds')){
            $this->_defaultFileName = 'feeds.txt';
            $configExportPath  = preg_replace('/\w+.csv/', 'feeds.txt', $configExportPath);
        }

        $mediapath = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        if ($configExportPath) {
            if (file_exists($this->_directoryList->getRoot().'/'.$configExportPath)) {
                $downloadLink = $this->_storeManager->getStore()->getBaseUrl().$configExportPath;
            }
        } else {
            if (file_exists($mediapath->getAbsolutePath().$this->_defaultFileName)) {
                $downloadLink = $this->_storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $this->_defaultFileName;
            }
        }

        return $downloadLink;
    }

    public function getConfigUrl()
    {
        return $this->_urlBuilder->getUrl('adminhtml/system_config/edit', ['section' => 'export_section']);
    }
}
