<?php

namespace RemiTheFox\PhpGPIO\Exception;

use RemiTheFox\PhpGPIO\Exception as BaseException;

class IOError extends BaseException {

    public function __construct() {
        parent::__construct('GPIO I/O Error.');
    }

}
