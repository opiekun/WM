<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

namespace Photoslurp\Pswidget\Controller\Adminhtml\Items;

class Index extends \Photoslurp\Pswidget\Controller\Adminhtml\Items
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Photoslurp_Pswidget::pswidget');
        $resultPage->getConfig()->getTitle()->prepend(__('Photoslurp Items'));
        $resultPage->addBreadcrumb(__('Photoslurp'), __('Photoslurp'));
        $resultPage->addBreadcrumb(__('Items'), __('Items'));
        return $resultPage;
    }
}
