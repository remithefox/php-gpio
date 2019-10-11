<?php

namespace RemiTheFox\PhpGPIO\Exception;

use RemiTheFox\PhpGPIO\Exception as BaseException;

class PermissionDenied extends BaseException {

    public function __construct() {
        parent::__construct('User have no permission to use GPIO. Add user to `gpio` group.');
    }
}
