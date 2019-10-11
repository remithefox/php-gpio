<?php

namespace RemiTheFox\PhpGPIO\Exception;

use RemiTheFox\PhpGPIO\Exception as BaseException;

class InvalidDirection extends BaseException {

    public function __construct() {
        parent::__construct('Invalid direction.');
    }

}
