<?php

namespace spec\RemiTheFox\PhpGPIO;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\ObjectBehavior;
use RemiTheFox\PhpGPIO\Exception\ExportTimeout;
use RemiTheFox\PhpGPIO\Exception\GpioNotFound;
use RemiTheFox\PhpGPIO\Exception\InvalidDirection;
use RemiTheFox\PhpGPIO\Exception\PermissionDenied;
use RemiTheFox\PhpGPIO\Exception\PinOccupied;
use RemiTheFox\PhpGPIO\Exception\WriteOnInputMode;
use RemiTheFox\PhpGPIO\Pin;
use function expect;

class PinSpec extends ObjectBehavior {

    /**
     * @var vfsStreamDirectory
     */
    private $testDir;

    public function let() {
        $this->testDir = vfsStream::setup('testDir');
    }

    function it_is_initializable() {
        $this->beConstructedWith(11, 'in', false, 10000, false, 'vfs://testDir/');
        $this->shouldHaveType(Pin::class);
    }

    public function it_validates_direction() {
        $this->beConstructedWith(11, 'meow', false, 10000, false, 'vfs://testDir/');
        $this->shouldThrow(InvalidDirection::class)->duringInstantiation();
    }

    public function it_throws_exception_on_gpio_not_found() {
        $this->beConstructedWith(15, 'out', false, 10000, false, 'vfs://testDir/not-existing-dir/');
        $this->shouldThrow(GpioNotFound::class)->duringInstantiation();
    }

    public function it_throws_exception_on_gpio_pin_occupied() {
        $this->createVirtualFile('export', '');
        $this->createVirtualFile('unexport', '');
        $this->createVirtualFile('gpio6/direction', '');
        $this->createVirtualFile('gpio6/value', '');
        $this->beConstructedWith(6, 'out', false, 10000, false, 'vfs://testDir/');
        $this->shouldThrow(PinOccupied::class)->duringInstantiation();
    }

    public function it_throws_exception_on_permission_denied() {
        $this->beConstructedWith(21, 'out', true, 10000, false, 'vfs://testDir/');
        $this->createVirtualFile('export', '', 0);
        $this->createVirtualFile('unexport', '', 0);
        $this->shouldThrow(PermissionDenied::class)->duringInstantiation();
    }

    public function it_throws_exception_on_timeout() {
        $this->beConstructedWith(37, 'out', true, 100, false, 'vfs://testDir/');
        $this->createVirtualFile('export', '');
        $this->createVirtualFile('unexport', '');
        $this->shouldThrow(ExportTimeout::class)->duringInstantiation();
    }

    public function it_can_set_value_to_high() {
        $this->createVirtualFile('export', '');
        $this->createVirtualFile('unexport', '');
        $this->createVirtualFile('gpio16/direction', '');
        $this->createVirtualFile('gpio16/value', '');
        $this->beConstructedWith(16, 'out', true, 10000, false, 'vfs://testDir/');
        $this->setValue(true)->shouldReturn($this);
        $this->getValue()->shouldReturn(true);
        expect($this->readVirtualFile('gpio16/value'))->toBe('1');
    }

    public function it_can_set_value_to_low() {
        $this->createVirtualFile('export', '');
        $this->createVirtualFile('unexport', '');
        $this->createVirtualFile('gpio18/direction', '');
        $this->createVirtualFile('gpio18/value', '');
        $this->beConstructedWith(18, 'out', true, 10000, false, 'vfs://testDir/');
        $this->setValue(false)->shouldReturn($this);
        $this->getValue()->shouldReturn(false);
        expect($this->readVirtualFile('gpio18/value'))->toBe('0');
    }

    public function it_throw_exception_when_set_value_on_input_mode() {
        $this->createVirtualFile('export', '');
        $this->createVirtualFile('unexport', '');
        $this->createVirtualFile('gpio3/direction', '');
        $this->createVirtualFile('gpio3/value', '');
        $this->beConstructedWith(3, 'in', true, 10000, false, 'vfs://testDir/');
        $this->shouldThrow(WriteOnInputMode::class)->during('setValue', [true]);
    }

    public function it_can_set_direction_to_in() {
        $this->createVirtualFile('export', '');
        $this->createVirtualFile('unexport', '');
        $this->createVirtualFile('gpio8/direction', '');
        $this->createVirtualFile('gpio8/value', '');
        $this->beConstructedWith(8, 'out', true, 10000, false, 'vfs://testDir/');
        $this->setDirection('in')->shouldReturn($this);
        $this->getDirection()->shouldReturn('in');
        $this->isInput()->shouldReturn(true);
        $this->isOutput()->shouldReturn(false);
        expect($this->readVirtualFile('gpio8/direction'))->toBe('in');
    }

    public function it_can_set_direction_to_out() {
        $this->createVirtualFile('export', '');
        $this->createVirtualFile('unexport', '');
        $this->createVirtualFile('gpio10/direction', '');
        $this->createVirtualFile('gpio10/value', '');
        $this->beConstructedWith(10, 'in', true, 10000, false, 'vfs://testDir/');
        $this->setDirection('out')->shouldReturn($this);
        $this->getDirection()->shouldReturn('out');
        $this->isInput()->shouldReturn(false);
        $this->isOutput()->shouldReturn(true);
        expect($this->readVirtualFile('gpio10/direction'))->toBe('out');
    }

    public function it_throw_exception_on_set_direction_to_invalid() {
        $this->createVirtualFile('export', '');
        $this->createVirtualFile('unexport', '');
        $this->createVirtualFile('gpio24/direction', '');
        $this->createVirtualFile('gpio24/value', '');
        $this->beConstructedWith(24, 'in', true, 10000, false, 'vfs://testDir/');
        $this->shouldThrow(InvalidDirection::class)->during('setDirection', ['meow']);
    }

    private function createVirtualFile($path, $content, $permissions = 0777) {
        $file = vfsStream::newFile($path);
        $file->setContent($content)->chmod($permissions);
        $this->testDir->addChild($file);
    }

    private function readVirtualFile($path) {
        return file_get_contents('vfs://testDir/' . $path);
    }

}
