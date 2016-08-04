<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Throwable;

/**
 * An operation has timed out.
 */
interface TimeoutException extends Throwable
{
}
