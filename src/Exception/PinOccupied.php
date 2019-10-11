<?php

namespace RemiTheFox\PhpGPIO\Exception;

use RemiTheFox\PhpGPIO\Exception as BaseException;

class PinOccupied extends BaseException {

    public function __construct() {
        parent::__construct('GPIO Pin is occupied. Use $force=true to ignore.');
    }

}
