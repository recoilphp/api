<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Eloquent\Phony\Phony;
use Error;
use Recoil\Strand;

describe(StrandException::class, function () {

    beforeEach(function () {
        $this->strand = Phony::mock(Strand::class);
        $this->strand->id->returns(123);
        $this->cause = new Error('<message>');

        $this->subject = StrandException::create(
            $this->strand->get(),
            $this->cause
        );
    });

    it('produces a useful message', function () {
        expect($this->subject->getMessage())->to->equal(
            'Unhandled exception in strand #123: Error (<message>).'
        );
    });

    it('exposes the failed strand', function () {
        expect($this->subject->strand())->to->equal($this->strand->get());
    });

    it('exposes the cause exception', function () {
        expect($this->subject->getPrevious())->to->equal($this->cause);
    });

});
