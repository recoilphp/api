<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Recoil;

use Eloquent\Phony\Phony;

describe(Recoil::class, function () {
    context('n-ary operations', function () {
        $this->strand = Phony::mock(Strand::class)->get();

        $operations = [
            'execute' => ['<coroutine>'],
            'callback' => ['<coroutine>'],
            'sleep' => [12.3],
            'timeout' => [12.3, '<coroutine>'],
            'suspend' => [null, null],
            'link' => [$this->strand, null],
            'unlink' => [$this->strand, null],
            'adopt' => [$this->strand],
            'all' => ['<coroutine-1>', '<coroutine-2>'],
            'any' => ['<coroutine-1>', '<coroutine-2>'],
            'some' => [123, '<coroutine-1>', '<coroutine-2>'],
            'first' => ['<coroutine-1>', '<coroutine-2>'],
            'read' => ['<stream>', 123, 456],
            'write' => ['<stream>', '<buffer>', 123],
        ];

        foreach ($operations as $name => $arguments) {
            it('::' . $name . '()', function () use ($name, $arguments) {
                $fn = [Recoil::class, $name];
                $result = $fn(...$arguments);
                expect($result)->to->be->an->instanceof(ApiCall::class);
                expect($result->__name)->to->equal($name);
                expect($result->__arguments)->to->equal($arguments);
            });
        }
    });

    context('nullary operations', function () {
        $operations = [
            'cooperate',
            'strand',
            'terminate',
            'stop',
        ];

        foreach ($operations as $name) {
            it('::' . $name . '()', function () use ($name, $arguments) {
                $fn = [Recoil::class, $name];
                $result = $fn();
                expect($result)->to->be->an->instanceof(ApiCall::class);
                expect($result->__name)->to->equal($name);
                expect($result->__arguments)->to->equal([]);

                expect($fn())->to->equal($result);
            });
        }
    });

    it('supports non-standard operations', function () {
        $result = Recoil::aNonStandardOperation(1, 2, 3);
        expect($result)->to->be->an->instanceof(ApiCall::class);
        expect($result->__name)->to->equal('aNonStandardOperation');
        expect($result->__arguments)->to->equal([1, 2, 3]);
    });
});
