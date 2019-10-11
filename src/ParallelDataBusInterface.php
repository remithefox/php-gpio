<?php

namespace RemiTheFox\PhpGPIO;

use RemiTheFox\PhpGPIO\Exception\OutOfRange;
use RemiTheFox\PhpGPIO\Exception\WriteOnInputMode;

interface ParallelDataBusInterface extends GpioElementInterface {

    /**
     * Read value from data bus
     * @return int value unsigned int in range 0 .. 2^n-1
     */
    public function getValue();

    /**
     * Write value to data bus
     * @param int $value value - unsigned int in range 0 .. 2^n-1
     * @return $this
     * @throws WriteOnInputMode when trying to write value on GPIO element on input mode
     * @throws OutOfRange when trying to write value out of range from 0 to 2<sup>n</sup>-1 on `ParallelDataBus`
     * @throws IOError on any input/output error
     */
    public function setValue($value);

    /**
     * Get number of pins
     * @return int number of pins
     */
    public function countPins();
}
