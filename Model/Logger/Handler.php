<?php

namespace DigitalHub\Automation\Model\Logger;

use Monolog\Logger as MonologLogger;
use Magento\Framework\Logger\Handler\Base as HandlerBase;

class Handler extends HandlerBase {

    /**
     * Loggin level type
     * @var int
     */
    protected $loggerType = MonologLogger::INFO;

    /**
     * File path
     * @var string
     */
    protected $fileName = "/var/log/digitalhub_api_order.log";
}
