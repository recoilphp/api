<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Eloquent\Phony\Phpunit\Phony;
use PHPUnit\Framework\TestCase;
use Throwable;

class CompositeExceptionTest extends TestCase
{
    private $exception1;
    private $exception2;
    private $exceptions;
    private $subject;

    public function setUp(): void {
        $this->exception1 = Phony::mock(Throwable::class)->get();
        $this->exception2 = Phony::mock(Throwable::class)->get();
        $this->exceptions = [
            1 => $this->exception1,
            0 => $this->exception2,
        ];

        $this->subject = CompositeException::create($this->exceptions);
    }

    public function testMessage() {
        $this->assertEquals(
            '2 operation(s) failed.',
            $this->subject->getMessage(),
        );
    }

    public function testKeysAndOrderArePreserved() {
        $exceptions = $this->subject->exceptions();
        $this->assertSame($this->exceptions, $exceptions);
    }
}
