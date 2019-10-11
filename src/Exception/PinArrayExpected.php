<?php

namespace RemiTheFox\PhpGPIO\Exception;

use RemiTheFox\PhpGPIO\Exception as BaseException;

class PinArrayExpected extends BaseException {

    public function __construct() {
        parent::__construct('All array element have to be PinInterface.');
    }
}
