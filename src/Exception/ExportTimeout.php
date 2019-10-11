<?php

namespace RemiTheFox\PhpGPIO\Exception;

use RemiTheFox\PhpGPIO\Exception as BaseException;

class ExportTimeout extends BaseException {

    public function __construct() {
        parent::__construct('GPIO export timeout.');
    }
}
