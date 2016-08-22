<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Error;

describe(KernelException::class, function () {
    beforeEach(function () {
        $this->cause = new Error('<message>');

        $this->subject = KernelException::create($this->cause);
    });

    it('produces a useful message', function () {
        expect($this->subject->getMessage())->to->equal(
            'Unhandled exception in kernel: Error (<message>).'
        );
    });

    it('exposes the cause exception', function () {
        expect($this->subject->getPrevious())->to->equal($this->cause);
    });
});
