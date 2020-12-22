<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Recoil;

use Eloquent\Phony\Phpunit\Phony;
use PHPUnit\Framework\TestCase;

class RecoilTest extends TestCase
{
    /**
     * @dataProvider operations
     */
    public function testOperations(string $name, array $arguments) {
        $fn = [Recoil::class, $name];
        $result = $fn(...$arguments);

        $this->assertInstanceOf(ApiCall::class, $result);
        $this->assertEquals($name, $result->__name);
        $this->assertEquals($arguments, $result->__arguments);
    }

    public function operations(): array {
        $strand = Phony::mock(Strand::class)->get();

        return [
            ['stop', []],
            ['execute', ['<coroutine>']],
            ['callback', ['<coroutine>']],
            ['cooperate', []],
            ['sleep', [12.3]],
            ['timeout', [12.3, '<coroutine>']],
            ['strand', []],
            ['suspend', [null, null]],
            ['terminate', []],
            ['link', [$strand, null]],
            ['unlink', [$strand, null]],
            ['adopt', [$strand]],
            ['all', ['<coroutine-1>', '<coroutine-2>']],
            ['any', ['<coroutine-1>', '<coroutine-2>']],
            ['some', [123, '<coroutine-1>', '<coroutine-2>']],
            ['first', ['<coroutine-1>', '<coroutine-2>']],
            ['read', ['<stream>', 123, 456]],
            ['write', ['<stream>', '<buffer>', 123]],
            ['select', [['<stream-1>'], ['<stream-2>']]],
        ];
    }

    public function testNonStandardOperations() {
        $result = Recoil::aNonStandardOperation(1, 2, 3);

        $this->assertInstanceOf(ApiCall::class, $result);
        $this->assertEquals('aNonStandardOperation', $result->__name);
        $this->assertEquals([1, 2, 3], $result->__arguments);
    }
}
