<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

namespace Photoslurp\Pswidget\Controller\Adminhtml\Export;

class Index extends \Photoslurp\Pswidget\Controller\Adminhtml\Export
{
    /**
     * Export.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Photoslurp_Pswidget::export');
        $resultPage->getConfig()->getTitle()->prepend(__('Photoslurp Export'));
        $resultPage->addBreadcrumb(__('Photoslurp'), __('Photoslurp'));
        $resultPage->addBreadcrumb(__('Export'), __('Export'));
        return $resultPage;
    }
}
