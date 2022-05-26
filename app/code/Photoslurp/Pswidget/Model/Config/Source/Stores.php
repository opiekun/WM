<?php

namespace Photoslurp\Pswidget\Model\Config\Source;

class Stores implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $systemStore = $objectManager->get('\Magento\Store\Model\System\Store');
        return $systemStore->getStoreValuesForForm(false, true);
    }
}
