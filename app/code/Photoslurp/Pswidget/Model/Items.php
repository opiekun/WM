<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

namespace Photoslurp\Pswidget\Model;

class Items extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Photoslurp\Pswidget\Model\ResourceModel\Items');
    }
}
