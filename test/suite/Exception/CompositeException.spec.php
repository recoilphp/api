<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Eloquent\Phony\Phony;
use Throwable;

describe(CompositeException::class, function () {

    beforeEach(function () {
        $this->exception1 = Phony::mock(Throwable::class)->get();
        $this->exception2 = Phony::mock(Throwable::class)->get();
        $this->exceptions = [
            1 => $this->exception1,
            0 => $this->exception2,
        ];

        $this->subject = CompositeException::create($this->exceptions);
    });

    it('produces a useful message', function () {
        expect($this->subject->getMessage())->to->equal(
            '2 operation(s) failed.'
        );
    });

    it('preserves the exception array keys and order', function () {
        $exceptions = $this->subject->exceptions();
        expect($exceptions)->to->have->keys([1, 0]);
        expect($exceptions[1] === $this->exception1);
        expect($exceptions[0] === $this->exception2);
    });

});
