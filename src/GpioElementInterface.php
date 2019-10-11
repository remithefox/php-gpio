<?php

namespace RemiTheFox\PhpGPIO;

use RemiTheFox\PhpGPIO\Exception\InvalidDirection;
use RemiTheFox\PhpGPIO\Exception\WriteOnInputMode;

interface GpioElementInterface {

    const DIRECTION_IN = 'in';
    const DIRECTION_OUT = 'out';

    /**
     * Read GPIO element value
     * @return mixed GPIO element value
     * @throws IOError on any input/output error
     */
    public function getValue();

    /**
     * Write GPIO element value
     * @param mixed $value GPIO element value
     * @return $this
     * @throws WriteOnInputMode when trying to write value on GPIO element on input mode
     * @throws IOError on any input/output error
     */
    public function setValue($value);

    /**
     * Returns direction
     * @return string GpioElementInterface::DIRECTION_IN or GpioElementInterface::DIRECTION_OUT
     */
    public function getDirection();

    /**
     * Set direction
     * @param string $direction GpioElementInterface::DIRECTION_IN or GpioElementInterface::DIRECTION_OUT
     * @return $this
     * @throws InvalidDirection when direction will be different than `'in'` or `'out'`
     * @throws IOError on any input/output error
     */
    public function setDirection($direction);

    /**
     * Check if GPIO element is in input mode
     * @return bool true on input mode
     */
    public function isInput();

    /**
     * Check if GPIO element is in output mode
     * @return bool true on output mode
     */
    public function isOutput();

    /**
     * GPIO element will be released after unset or lost the last reference.
     */
    public function enableAutorelease();

    /**
     * GPIO element will not be released after unset or lost the last reference.
     */
    public function disableAutorelease();
}
