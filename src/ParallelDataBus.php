<?php

namespace RemiTheFox\PhpGPIO;

use RemiTheFox\PhpGPIO\Exception\InvalidDirection;
use RemiTheFox\PhpGPIO\Exception\OutOfRange;
use RemiTheFox\PhpGPIO\Exception\PinArrayExpected;
use RemiTheFox\PhpGPIO\Exception\WriteOnInputMode;

class ParallelDataBus implements ParallelDataBusInterface {

    /**
     * @var PinInterface[]
     */
    private $pins;

    /**
     * Bus mode
     * @var string in or out
     */
    private $direction;

    /**
     * Bus value in output mode
     * @var bool
     */
    private $value = false;

    /**
     * Max value
     * @var int
     */
    private $maxValue;

    /**
     * Creates ParallelDataBus object
     * @param PinInterface[] $pins array of pins
     * @param string $direction GpioElementInterface::DIRECTION_IN or GpioElementInterface::DIRECTION_OUT
     * @throws InvalidDirection when direction will be different than `'in'` or `'out'`
     * @throws PinArrayExpected when `ParallelDataBus` gets something other than array of pins in first parameter of constructor.
     * @throws IOError on any input/output error
     */
    public function __construct(array $pins,
            $direction = GpioElementInterface::DIRECTION_IN) {
        $this->validatePins($pins);
        $this->validateDirection($direction);
        $this->pins = array_values($pins);
        $this->direction = $direction;
        $this->maxValue = pow(2, count($pins)) - 1;
        $this->setup();
    }

    /**
     * @{inheritdoc}
     */
    public function getValue() {
        if ($this->direction == GpioElementInterface::DIRECTION_IN) {
            $value = 0;
            foreach ($this->pins as $number => $pin) {
                $value |= $pin->getValue() << $number;
            }
            return $value;
        }
        return $this->value;
    }

    /**
     * @{inheritdoc}
     */
    public function setValue($value) {
        if ($this->direction == GpioElementInterface::DIRECTION_IN) {
            throw new WriteOnInputMode();
        }
        $this->validateValue($value);
        $this->value = $value;
        foreach ($this->pins as $number => $pin) {
            $pin->setValue((bool) ($value & 0x01 << $number));
        }
        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function getDirection() {
        return $this->direction;
    }

    /**
     * @{inheritdoc}
     */
    public function setDirection($direction) {
        $this->validateDirection($direction);
        $this->direction = $direction;
        foreach ($this->pins as $pin) {
            $pin->setDirection($direction);
        }
        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function countPins() {
        return count($this->pins);
    }

    /**
     * @{inheritdoc}
     */
    public function isInput(): bool {
        return $this->direction == GpioElementInterface::DIRECTION_IN;
    }

    /**
     * @{inheritdoc}
     */
    public function isOutput(): bool {
        return $this->direction == GpioElementInterface::DIRECTION_OUT;
    }

    /**
     * @{inheritdoc}
     */
    public function disableAutorelease() {
        foreach ($this->pins as $pin) {
            $pin->disableAutorelease();
        }
        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function enableAutorelease() {
        foreach ($this->pins as $pin) {
            $pin->enableAutorelease();
        }
        return $this;
    }

    /**
     * Validates direction
     * @param string $direction
     * @throws InvalidDirection when direction will be different than `'in'` or `'out'`
     */
    private function validateDirection($direction) {
        if (!in_array($direction, [GpioElementInterface::DIRECTION_IN, GpioElementInterface::DIRECTION_OUT])) {
            throw new InvalidDirection();
        }
    }

    /**
     * Validates pin array
     * @param PinInterface[] $pins
     * @throws PinArrayExpected will be thrown when `ParallelDataBus` gets something other than array of pins in first parameter of constructor.
     */
    private function validatePins(array $pins) {
        foreach ($pins as $pin) {
            if (!$pin instanceof PinInterface) {
                throw new PinArrayExpected();
            }
        }
    }

    /**
     * Validates if value is in range 0..2^n-1
     * @param int $value
     * @throws when trying to write value out of range from 0 to 2<sup>n</sup>-1 on `ParallelDataBus`
     */
    private function validateValue($value) {
        if (!is_int($value) || $value < 0 || $value > $this->maxValue) {
            throw new OutOfRange();
        }
    }

    /**
     * Setup paralel data bus
     */
    private function setup() {
        foreach ($this->pins as $pin) {
            if (!$pin instanceof PinInterface) {
                $pin->setDirection($this->direction);
            }
        }
    }

}
