<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil;

/**
 * Please note that this code is not part of the public API. It may be
 * changed or removed at any time without notice.
 *
 * @access private
 */
final class ApiCall
{
    /**
     * @param string The operation name, corresponds to the methods in Api.
     */
    public $__name;

    /**
     * @param array The operation arguments.
     */
    public $__arguments;

    /**
     * @param string    $name      The operation name, corresponds to the methods in Api.
     * @param mixed,... $arguments The operation arguments.
     */
    public function __construct(string $name, ...$arguments)
    {
        $this->__name = $name;
        $this->__arguments = $arguments;
    }
}
