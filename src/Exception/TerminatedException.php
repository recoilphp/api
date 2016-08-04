<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Throwable;

/**
 * Indicates that a strand has been explicitly terminated.
 */
interface TerminatedException extends Throwable
{
    /**
     * Get the terminated strand.
     */
    public function strand() : Strand;
}
