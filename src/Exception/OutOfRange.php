<?php

namespace RemiTheFox\PhpGPIO\Exception;

use RemiTheFox\PhpGPIO\Exception as BaseException;

class OutOfRange extends BaseException {

    public function __construct() {
        parent::__construct('Value is out of range. I should be between 0 and (2^n)-1');
    }
}
