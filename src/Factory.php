<?php

namespace RemiTheFox\PhpGPIO;

class Factory {

    private function __construct() {
        
    }

    /**
     * Creates parallel data bus object and setup assigned GPIO pins
     * @param int[] $pinNumbers array of BCM pin number
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
    static public function createParallelDataBus(array $pinNumbers,
            $direction = GpioElementInterface::DIRECTION_IN, $force = false,
            $timeout = 10000, $autorelease = true, $devicePath = '/sys/class/gpio/') {
        $pins = [];
        foreach ($pinNumbers as $pinNumber) {
            $pins[] = new Pin($pinNumber, $direction, $force, $timeout, $autorelease, $devicePath);
        }
        return new ParallelDataBus($pins, $direction);
    }

}
