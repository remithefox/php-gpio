<?php

namespace RemiTheFox\PhpGPIO;

use RemiTheFox\PhpGPIO\Exception\ExportTimeout;
use RemiTheFox\PhpGPIO\Exception\GpioNotFound;
use RemiTheFox\PhpGPIO\Exception\InvalidDirection;
use RemiTheFox\PhpGPIO\Exception\IOError;
use RemiTheFox\PhpGPIO\Exception\PermissionDenied;
use RemiTheFox\PhpGPIO\Exception\PinOccupied;
use RemiTheFox\PhpGPIO\Exception\WriteOnInputMode;

class Pin implements PinInterface {

    /**
     * Pin number
     * @var int
     */
    private $pinNumber;

    /**
     * Pin direction
     * @var string in or out
     */
    private $direction;

    /**
     * Pin value in output mode
     * @var bool
     */
    private $value = false;

    /**
     * Value stream
     * @var resource
     */
    private $stream;

    /**
     * Release after object destruct
     * @var bool
     */
    private $autorelease;

    /**
     * Path to device
     * @var string
     */
    private $devicePath;

    /**
     * Creates Pin object and setup assigned GPIO pin
     * @param int $pinNumber BCM pin number
     * @param string $direction GpioElementInterface::DIRECTION_IN or GpioElementInterface::DIRECTION_OUT
     * @param bool $force allows force when pin is occupied
     * @param int $timeout export timeout in microseconds
     * @param bool $autorelease pin will be released after unset or lost the last reference
     * @param string $devicePath device path (almost sure you should leave it default, it's for testing)
     * @throws InvalidDirection when direction will be different than `'in'` or `'out'`
     * @throws GpioNotFound when GPIO is not found in system
     * @throws PermissionDenied when user does not have permission to use GPIO
     * @throws PinOccupied when chosen pin is occupied (is exported) and `$force` flag is false
     * @throws ExportTimeout when GPIO pin export time limit will be exceeded
     * @throws IOError on any input/output error
     */
    public function __construct($pinNumber,
            $direction = GpioElementInterface::DIRECTION_IN, $force = false,
            $timeout = 10000, $autorelease = true, $devicePath = '/sys/class/gpio/') {
        $this->validateDirection($direction);
        $this->pinNumber = $pinNumber;
        $this->direction = $direction;
        $this->autorelease = $autorelease;
        $this->devicePath = $devicePath;
        $this->setup($force, $timeout);
    }

    /**
     * Releases GPIO pin when autorelease is enabled
     */
    public function __destruct() {
        if ($this->autorelease) {
            $this->release();
        }
    }

    /**
     * @{inheritdoc}
     */
    public function getValue() {
        if ($this->direction == GpioElementInterface::DIRECTION_IN) {
            if (fseek($this->stream, 0) !== 0) {
                throw new IOError();
            }
            $value = fgets($this->stream, 2);
            if ($value === false) {
                throw new IOError();
            }
            return (bool) $value;
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
        $this->value = (bool) $value;
        fputs($this->stream, $value ? 1 : 0);
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
        $directionPath = $this->getDirectionPath();
        $this->direction = $direction;
        if (file_put_contents($directionPath, $this->direction) === false) {
            throw new IOError();
        }
        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function isInput() {
        return $this->direction == GpioElementInterface::DIRECTION_IN;
    }

    /**
     * @{inheritdoc}
     */
    public function isOutput() {
        return $this->direction == GpioElementInterface::DIRECTION_OUT;
    }

    /**
     * @{inheritdoc}
     */
    public function disableAutorelease() {
        $this->autorelease = false;
        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function enableAutorelease() {
        $this->autorelease = true;
        return $this;
    }

    /**
     * Setup GPIO pin
     * @param bool $force allows force when pin is occupied
     * @param int $timeout export timeout in microseconds
     * @throws GpioNotFound when GPIO is not found in system
     * @throws PermissionDenied when user does not have permission to use GPIO
     * @throws PinOccupied when chosen pin is occupied (is exported) and `$force` flag is false
     * @throws ExportTimeout when GPIO pin export time limit will be exceeded
     * @throws IOError on any input/output error
     */
    private function setup($force = false, $timeout = 10000) {
        $exportPath = $this->getExportPath();
        $unexportPath = $this->getUnexportPath();
        $directionPath = $this->getDirectionPath();
        $valuePath = $this->getValuePath();
        if (!file_exists($exportPath)) {
            throw new GpioNotFound();
        }
        if (!is_writable($exportPath)) {
            throw new PermissionDenied();
        }
        if (file_exists($directionPath)) {
            if (!$force) {
                throw new PinOccupied();
            }
            if (file_put_contents($unexportPath, $this->pinNumber) === false) {
                throw new IOError();
            }
        }
        if (file_put_contents($exportPath, $this->pinNumber) === false) {
            throw new IOError();
        }
        $time = 0;
        while (!file_exists($directionPath) || !is_writable($directionPath)) {
            usleep(10);
            $time += 10;
            if ($time > $timeout) {
                throw new ExportTimeout();
            }
        }
        if (file_put_contents($directionPath, $this->direction) === false) {
            throw new IOError();
        }
        $this->stream = fopen($valuePath, 'r+');
        if ($this->stream === false) {
            throw new IOError();
        }
    }

    /**
     * Releases GPIO pin
     * @throws IOError on any input/output error
     */
    private function release() {
        $unexportPath = $this->getUnexportPath();
        if (!fclose($this->stream) || file_put_contents($unexportPath, $this->pinNumber) === false) {
            throw new IOError();
        }
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
     * Get export path
     * @return string export path
     */
    private function getExportPath() {
        return $this->devicePath . 'export';
    }

    /**
     * Get unexport path
     * @return string unexport path
     */
    private function getUnexportPath() {
        return $this->devicePath . 'unexport';
    }

    /**
     * Get direction path
     * @return string direction path
     */
    private function getDirectionPath() {
        return $this->devicePath . 'gpio' . $this->pinNumber . '/direction';
    }

    /**
     * Get value path
     * @return string value path
     */
    private function getValuePath() {
        return $this->devicePath . 'gpio' . $this->pinNumber . '/value';
    }

}
