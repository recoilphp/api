<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Error;
use PHPUnit\Framework\TestCase;

class KernelExceptionTest extends TestCase
{
    private $cause;
    private $subject;

    public function setUp(): void {
        $this->cause = new Error('<message>');

        $this->subject = KernelException::create($this->cause);
    }

    public function testMessage() {
        $this->assertEquals(
            'Unhandled exception in kernel: Error (<message>).',
            $this->subject->getMessage(),
        );
    }

    public function testPrevious() {
        $this->assertSame(
            $this->cause,
            $this->subject->getPrevious(),
        );
    }
}
