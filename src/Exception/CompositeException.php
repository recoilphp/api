<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Throwable;

/**
 * A container for multiple exceptions produced by API operations that run
 * multiple strands in parallel.
 */
interface CompositeException extends Throwable
{
    /**
     * Get the exceptions.
     *
     * The array order matches the order of strand completion. The array keys
     * indicate the order in which the strand was passed to the operation. This
     * allows unpacking of the result with list() to get the results in
     * pass-order.
     *
     * @return array<integer, Throwable> The exceptions.
     */
    public function exceptions() : array;
}
