<?php

namespace DigitalHub\Automation\Model;

use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger {

    /**
     * Log multiple errors
     * @param  array  $errors
     * @return void
     */
    public function logErrors(array $errors)
    {
        foreach ($errors as $error) {
            $this->log(self::INFO, '[ERROR] ' . $error);
        }
    }
}
