<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

namespace Photoslurp\Pswidget\Controller\Adminhtml\Items;

class NewAction extends \Photoslurp\Pswidget\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
