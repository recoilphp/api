<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Eloquent\Phony\Phony;
use Error;
use PHPUnit\Framework\TestCase;
use Recoil\Strand;

class StrandExceptionTest extends TestCase
{
    private $strand;
    private $cause;
    private $subject;

    public function setUp(): void {
        $this->strand = Phony::mock(Strand::class);
        $this->strand->id->returns(123);
        $this->cause = new Error('<message>');

        $this->subject = StrandException::create(
            $this->strand->get(),
            $this->cause
        );
    }

    public function testMessage() {
        $this->assertEquals(
            'Unhandled exception in strand #123: Error (<message>).',
            $this->subject->getMessage(),
        );
    }

    public function testStrandIsExposed() {
        $this->assertSame(
            $this->strand->get(),
            $this->subject->strand(),
        );
    }

    public function testPrevious() {
        $this->assertSame(
            $this->cause,
            $this->subject->getPrevious(),
        );
    }
}
