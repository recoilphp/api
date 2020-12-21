<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Eloquent\Phony\Phony;
use PHPUnit\Framework\TestCase;
use Recoil\Strand;

class TerminatedExceptionTest extends TestCase
{
    private $strand;
    private $cause;
    private $subject;

    public function setUp(): void {
        $this->strand = Phony::mock(Strand::class);
        $this->strand->id->returns(123);

        $this->subject = TerminatedException::create($this->strand->get());
    }

    public function testMessage() {
        $this->assertEquals(
            'Strand #123 was terminated.',
            $this->subject->getMessage(),
        );
    }

    public function testStrandIsExposed() {
        $this->assertSame(
            $this->strand->get(),
            $this->subject->strand(),
        );
    }
}
