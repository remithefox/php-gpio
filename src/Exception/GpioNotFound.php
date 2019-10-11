<?php

namespace RemiTheFox\PhpGPIO\Exception;

use RemiTheFox\PhpGPIO\Exception as BaseException;

class GpioNotFound extends BaseException {

    public function __construct() {
        parent::__construct('GPIO not found.');
    }
}
