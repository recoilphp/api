<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Recoil\Exception;

use PHPUnit\Framework\TestCase;

class TimeoutExceptionTest extends TestCase
{
    private $subject;

    public function setUp(): void {
        $this->subject = TimeoutException::create(1.25);
    }

    public function testMessage() {
        $this->assertEquals(
            'The operation timed out after 1.25 second(s).',
            $this->subject->getMessage(),
        );
    }
}
