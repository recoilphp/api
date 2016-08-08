<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil\Exception;

use Throwable;

/**
 * An operation can not be completed because the kernel has been stopped.
 */
interface KernelStoppedException extends Throwable
{
}
