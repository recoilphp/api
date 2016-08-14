<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil\Exception;

describe(TimeoutException::class, function () {

    it('produces a useful message', function () {
        $exception = TimeoutException::create(1.25);

        expect($exception->getMessage())->to->equal(
            'The operation timed out after 1.25 second(s).'
        );
    });

});
