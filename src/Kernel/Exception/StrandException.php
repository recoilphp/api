<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil\Kernel\Exception;

/*
 * A kernel panic has occurred as a result of an unhandled exception in a
 * strand.
 */
interface StrandException extends KernelPanicException
{
    /**
     * Get the strand that caused the exception.
     */
    public function strand() : Strand;
}
