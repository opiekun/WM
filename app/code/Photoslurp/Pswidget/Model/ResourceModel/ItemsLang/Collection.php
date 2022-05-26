<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

namespace Photoslurp\Pswidget\Model\ResourceModel\ItemsLang;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Photoslurp\Pswidget\Model\ItemsLang', 'Photoslurp\Pswidget\Model\ResourceModel\ItemsLang');
    }
}
