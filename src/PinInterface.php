<?php

namespace RemiTheFox\PhpGPIO;

interface PinInterface extends GpioElementInterface{

    /**
     * Read GPIO pin value
     * @return bool true on high false on low
     * @throws IOError on any input/output error
     */
    public function getValue();

    /**
     * Write GPIO pin value
     * @param bool $value true for high, false for low
     * @return $this
     * @throws WriteOnInputMode will be thrown when trying to write value on GPIO element on input mode
     * @throws IOError on any input/output error
     */
    public function setValue($value);

}
