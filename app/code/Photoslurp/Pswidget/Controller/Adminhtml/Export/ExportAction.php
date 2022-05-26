<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

namespace Photoslurp\Pswidget\Controller\Adminhtml\Export;

class ExportAction extends \Photoslurp\Pswidget\Controller\Adminhtml\Export
{
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $export = $objectManager->get('Photoslurp\Pswidget\Model\Export');
        try {
            $export->photoslurpExport();
            $this->messageManager->addSuccess(__('Products have been exported.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
        }

        $this->_redirect('photoslurp_pswidget/*/');
    }
}
