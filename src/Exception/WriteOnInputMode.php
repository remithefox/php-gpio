<?php

namespace RemiTheFox\PhpGPIO\Exception;

use RemiTheFox\PhpGPIO\Exception as BaseException;

class WriteOnInputMode extends BaseException {

    public function __construct() {
        parent::__construct('Cannot set value on input pin.');
    }

}
