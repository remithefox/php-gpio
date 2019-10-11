<?php

namespace spec\RemiTheFox\PhpGPIO;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RemiTheFox\PhpGPIO\Exception\OutOfRange;
use RemiTheFox\PhpGPIO\Exception\WriteOnInputMode;
use RemiTheFox\PhpGPIO\GpioElementInterface;
use RemiTheFox\PhpGPIO\ParallelDataBus;
use RemiTheFox\PhpGPIO\PinInterface;

class ParallelDataBusSpec extends ObjectBehavior {

    public function it_is_initializable(PinInterface $pin0, PinInterface $pin1,
            PinInterface $pin2, PinInterface $pin3, PinInterface $pin4,
            PinInterface $pin5, PinInterface $pin6, PinInterface $pin7) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3, $pin4, $pin5, $pin6, $pin7]);
        $this->shouldHaveType(ParallelDataBus::class);
    }

    public function it_should_read_value_properly(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3,
            PinInterface $pin4, PinInterface $pin5, PinInterface $pin6,
            PinInterface $pin7) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3, $pin4, $pin5, $pin6, $pin7]);
        $pin0->getValue()
                ->willReturn(true)
                ->shouldBeCalled();

        $pin1->getValue()
                ->willReturn(true)
                ->shouldBeCalled();

        $pin2->getValue()
                ->willReturn(false)
                ->shouldBeCalled();

        $pin3->getValue()
                ->willReturn(false)
                ->shouldBeCalled();

        $pin4->getValue()
                ->willReturn(false)
                ->shouldBeCalled();

        $pin5->getValue()
                ->willReturn(false)
                ->shouldBeCalled();

        $pin6->getValue()
                ->willReturn(true)
                ->shouldBeCalled();

        $pin7->getValue()
                ->willReturn(true)
                ->shouldBeCalled();

        $this->getValue()->shouldReturn(0xc3);
    }

    public function it_should_write_value_properly(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3,
            PinInterface $pin4, PinInterface $pin5, PinInterface $pin6,
            PinInterface $pin7) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3, $pin4, $pin5, $pin6, $pin7], GpioElementInterface::DIRECTION_OUT);
        $pin0->setValue(true)
                ->willReturn($pin0)
                ->shouldBeCalled();

        $pin1->setValue(false)
                ->willReturn($pin1)
                ->shouldBeCalled();

        $pin2->setValue(true)
                ->willReturn($pin2)
                ->shouldBeCalled();

        $pin3->setValue(false)
                ->willReturn($pin3)
                ->shouldBeCalled();

        $pin4->setValue(false)
                ->willReturn($pin4)
                ->shouldBeCalled();

        $pin5->setValue(true)
                ->willReturn($pin5)
                ->shouldBeCalled();

        $pin6->setValue(false)
                ->willReturn($pin6)
                ->shouldBeCalled();

        $pin7->setValue(true)
                ->willReturn($pin7)
                ->shouldBeCalled();


        $this->setValue(0xa5)->shouldReturn($this);
    }

    public function it_should_throw_exception_when_writing_on_input_mode(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3,
            PinInterface $pin4, PinInterface $pin5, PinInterface $pin6,
            PinInterface $pin7) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3, $pin4, $pin5, $pin6, $pin7], GpioElementInterface::DIRECTION_IN);
        $this->shouldThrow(WriteOnInputMode::class)->duringSetValue(0xaa);
    }

    public function it_should_throw_exception_when_writing_value_is_out_of_range(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3,
            PinInterface $pin4, PinInterface $pin5, PinInterface $pin6,
            PinInterface $pin7) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3, $pin4, $pin5, $pin6, $pin7], GpioElementInterface::DIRECTION_OUT);
        $this->shouldThrow(OutOfRange::class)->duringSetValue(0x101);
    }

    public function it_can_set_direction_to_input(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3
    ) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3], GpioElementInterface::DIRECTION_OUT);
        $pin0->setDirection(GpioElementInterface::DIRECTION_IN)
                ->willReturn($pin0)
                ->shouldBeCalled();
        $pin1->setDirection(GpioElementInterface::DIRECTION_IN)
                ->willReturn($pin1)
                ->shouldBeCalled();
        $pin2->setDirection(GpioElementInterface::DIRECTION_IN)
                ->willReturn($pin2)
                ->shouldBeCalled();
        $pin3->setDirection(GpioElementInterface::DIRECTION_IN)
                ->willReturn($pin3)
                ->shouldBeCalled();
        $this->setDirection(GpioElementInterface::DIRECTION_IN)->shouldReturn($this);
    }

    public function it_can_set_direction_to_output(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3
    ) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3], GpioElementInterface::DIRECTION_IN);
        $pin0->setDirection(GpioElementInterface::DIRECTION_OUT)
                ->willReturn($pin0)
                ->shouldBeCalled();
        $pin1->setDirection(GpioElementInterface::DIRECTION_OUT)
                ->willReturn($pin1)
                ->shouldBeCalled();
        $pin2->setDirection(GpioElementInterface::DIRECTION_OUT)
                ->willReturn($pin2)
                ->shouldBeCalled();
        $pin3->setDirection(GpioElementInterface::DIRECTION_OUT)
                ->willReturn($pin3)
                ->shouldBeCalled();
        $this->setDirection(GpioElementInterface::DIRECTION_OUT)->shouldReturn($this);
    }

    public function it_can_count_pins(PinInterface $pin0, PinInterface $pin1,
            PinInterface $pin2, PinInterface $pin3
    ) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3], GpioElementInterface::DIRECTION_IN);
        $this->countPins()->shouldReturn(4);
    }

    public function it_can_set_enable_autorelease(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3
    ) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3], GpioElementInterface::DIRECTION_IN);
        $pin0->enableAutorelease()
                ->willReturn($pin0)
                ->shouldBeCalled();
        $pin1->enableAutorelease()
                ->willReturn($pin1)
                ->shouldBeCalled();
        $pin2->enableAutorelease()
                ->willReturn($pin2)
                ->shouldBeCalled();
        $pin3->enableAutorelease()
                ->willReturn($pin3)
                ->shouldBeCalled();
        $this->enableAutorelease()->shouldReturn($this);
    }

    public function it_can_set_disable_autorelease(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3
    ) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3], GpioElementInterface::DIRECTION_IN);
        $pin0->disableAutorelease()
                ->willReturn($pin0)
                ->shouldBeCalled();
        $pin1->disableAutorelease()
                ->willReturn($pin1)
                ->shouldBeCalled();
        $pin2->disableAutorelease()
                ->willReturn($pin2)
                ->shouldBeCalled();
        $pin3->disableAutorelease()
                ->willReturn($pin3)
                ->shouldBeCalled();
        $this->disableAutorelease()->shouldReturn($this);
    }

    public function it_can_set_indicate_input_direction_correct(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3
    ) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3], GpioElementInterface::DIRECTION_IN);
        $this->getDirection()->shouldReturn(GpioElementInterface::DIRECTION_IN);
        $this->isInput()->shouldReturn(true);
        $this->isOutput()->shouldReturn(false);
    }

    public function it_can_set_indicate_output_direction_correct(PinInterface $pin0,
            PinInterface $pin1, PinInterface $pin2, PinInterface $pin3
    ) {
        $this->beConstructedWith([$pin0, $pin1, $pin2, $pin3], GpioElementInterface::DIRECTION_OUT);
        $this->getDirection()->shouldReturn(GpioElementInterface::DIRECTION_OUT);
        $this->isInput()->shouldReturn(false);
        $this->isOutput()->shouldReturn(true);
    }

}
