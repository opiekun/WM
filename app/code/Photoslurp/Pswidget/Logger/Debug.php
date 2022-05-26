<?php

namespace Photoslurp\Pswidget\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Debug extends Base
{
    protected $fileName = '/var/log/photoslurp_debug.log';
    protected $loggerType = Logger::DEBUG;
}