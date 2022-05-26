<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

namespace Photoslurp\Pswidget\Controller\Adminhtml\Items;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Photoslurp\Pswidget\Controller\Adminhtml\Items
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('Photoslurp\Pswidget\Model\Items');
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                    $params = array_map(
                        function () {
                            return null;
                        },
                        $model->getData()
                    );
                    $data = array_merge($params, $data);
                }

                if(isset($data['lookbook_product_types'])){
                    $data['lookbook_product_types'] = json_encode($data['lookbook_product_types']);
                }

                if(isset($data['category'])){
                    $data['category'] = json_encode($data['category']);
                }

                if(isset($data['website'])){
                    $data['website'] = json_encode($data['website']);
                }

                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();

                if (isset($data['css'])) {
                    $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
                    $writer = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);

                    $file = $writer->openFile('photoslurp/'.$data['widget_id'] . '.css', 'w');
                    try {
                        $file->lock();
                        try {
                            $file->write($data['css']);
                        }
                        finally {
                            $file->unlock();
                        }
                    }
                    finally {
                        $file->close();
                    }
                }

                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('photoslurp_pswidget/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('photoslurp_pswidget/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('photoslurp_pswidget/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('photoslurp_pswidget/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('photoslurp_pswidget/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('photoslurp_pswidget/*/');
    }
}
