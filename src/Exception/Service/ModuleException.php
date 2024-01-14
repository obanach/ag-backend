<?php

namespace App\Exception\Service;

class ModuleException extends \Exception {

        public function __construct($message = "", $code = 0, \Throwable $previous = null) {
            parent::__construct($message, $code, $previous);
        }

}