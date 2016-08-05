<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil;

use Recoil\Exception\CompositeException;
use Recoil\Exception\TerminatedException;
use Recoil\Exception\TimeoutException;
use Recoil\Kernel\Strand;
use Throwable;

/**
 * This class defines the standard Recoil kernel API operations. It contains no
 * implementation, but instead dispatches to the underlying API implementation
 * provided by the kernel.
 *
 * Each operation may be cooperative, or non-cooperative. Cooperative operations
 * allow the kernel to execute other strands before returning.
 *
 * The return type of each operation describes the value produced the yield
 * statement used to call the operation within a coroutine.
 *
 * @todo document dispatchable values (don't forget strand, awaitable, etc)
 */
abstract class Recoil
{
    /**
     * Schedule a coroutine for execution on a new strand.
     *
     * The coroutine can be a generator object, generator function, or any
     * dispatchable value as documented above.
     *
     * This operation is NON-COOPERATIVE, it returns before the new strand begins
     * execution.
     *
     * @param mixed $coroutine The coroutine to execute.
     *
     * @return Strand The new strand.
     */
    public static function execute($coroutine)
    {
        return new ApiCall(__FUNCTION__, $coroutine);
    }

    /**
     * Create a callback function that starts a new strand of execution.
     *
     * This operation can be used to run a coroutine from within conventional
     * callback-based asynchronous code.
     *
     * The coroutine can be a generator object, generator function, or any
     * dispatchable value as documented above. If the coroutine is a function
     * it is forwarded the arguments that are passed to the callback.
     *
     * This operation is NON-COOPERATIVE.
     *
     * @param mixed $coroutine The coroutine to execute.
     *
     * @return callable
     */
    public static function callback($coroutine)
    {
        return new ApiCall(__FUNCTION__, $coroutine);
    }

    /**
     * Force the current strand to cooperate.
     *
     * Allows other strands to execute before returning. By definition, this
     * operation is COOPERATIVE.
     */
    public static function cooperate()
    {
        static $call;

        if ($call === null) {
            $call = new ApiCall(__FUNCTION__);
        }

        return $call;
    }

    /**
     * Suspend the current strand for a fixed interval.
     *
     * This operation is always COOPERATIVE, even if the interval is zero or
     * less.
     *
     * @param float $interval The interval to wait, in seconds.
     */
    public static function sleep(float $interval)
    {
        return new ApiCall(__FUNCTION__, $interval);
    }

    /**
     * Execute a coroutine with a cap on execution time.
     *
     * The coroutine can be a generator object, generator function, or any
     * dispatchable value as documented above.
     *
     * The coroutine is executed on its own strand. If the strand does not exit
     * within the specified time it is terminated.
     *
     * This operation is COOPERATIVE. The current strand is suspended until the
     * coroutine returns or throws.
     *
     * @param float $timeout   The maximum time to allow for execution, in seconds.
     * @param mixed $coroutine The coroutine to execute.
     *
     * @return mixed            The return value of the coroutine.
     * @throws Throwable        The exception thrown by the coroutine, if any.
     * @throws TimeoutException The operation has timed out.
     */
    public static function timeout(float $timeout, $coroutine)
    {
        return new ApiCall(__FUNCTION__, $timeout, $coroutine);
    }

    /**
     * Get the current strand.
     *
     * This operation is NON-COOPERATIVE.
     *
     * @return Strand The current strand.
     */
    public static function strand()
    {
        static $call;

        if ($call === null) {
            $call = new ApiCall(__FUNCTION__);
        }

        return $call;
    }

    /**
     * Suspend execution of the calling strand until it is manually resumed or
     * terminated.
     *
     * This operation is typically used to integrate coroutines with other forms
     * of asynchronous code.
     *
     * The $suspendFn argument is invoked immediately with the current strand
     * as its only parameter. This function may, for example, start an operation
     * that the strand must wait for, and store the strand so that it may be
     * resumed in the future.
     *
     * The $terminateFn callable is invoked if the strand is terminated before
     * it is resumed. It is also passed the strand as its only parameter. It may
     * be used to cancel and cleanup any operations performed by the $suspendFn.
     *
     * While suspending would typically be a cooperative operation, $suspendFn
     * is, by design, able to resume the strand immediately. Therefore no
     * guarantees can be made about the cooperativeness of suspend() without
     * knowing the implementation of $suspendFn.
     *
     * Strands implement the Listener interface:
     * @see Listener::send() to resume a strand with a value.
     * @see Listener::throw() to resume a strand with an exception.
     *
     * @param callable|null $suspendFn   A function invoked with the strand after it is suspended.
     * @param callable|null $terminateFn A function invoked if the strand is terminated while suspended.
     *
     * @return mixed     The value that the strand is resumed with.
     * @throws Throwable The exception that the strand is resumed with, if any.
     */
    public static function suspend(callable $suspendFn = null, callable $terminateFn = null)
    {
        return new ApiCall(__FUNCTION__, $suspendFn, $terminateFn);
    }

    /**
     * Terminate the current strand.
     *
     * This operation never returns, and is therefore COOPERATIVE.
     */
    public static function terminate()
    {
        static $call;

        if ($call === null) {
            $call = new ApiCall(__FUNCTION__);
        }

        return $call;
    }

    /**
     * Stop the kernel.
     *
     * This operation never returns.
     */
    public static function stop()
    {
        static $call;

        if ($call === null) {
            $call = new ApiCall(__FUNCTION__);
        }

        return $call;
    }

    /**
     * Create a bi-directional link between two strands.
     *
     * If either strand exits, the other is terminated.
     *
     * This method is an idempotent assertion that the link exists. It is not an
     * error to link two strands that are already linked.
     *
     * This operation is NON-COOPERATIVE.
     *
     * @param Strand      $strandA The first strand to link.
     * @param Strand|null $strandB The first strand to link (null = current strand).
     */
    public static function link(Strand $strandA, Strand $strandB = null)
    {
        return new ApiCall(__FUNCTION__, $strandA, $strandB);
    }

    /**
     * Break a previously established bi-directional link between strands.
     *
     * This method is an idempotent assertion that no link exists. It is not an
     * error to unlink two strands that are not already linked.
     *
     * This operation is NON-COOPERATIVE.
     *
     * @param Strand      $strandA The first strand to unlink.
     * @param Strand|null $strandB The first strand to unlink (null = current strand).
     */
    public static function unlink(Strand $strandA, Strand $strandB = null)
    {
        return new ApiCall(__FUNCTION__, $strandA, $strandB);
    }

    /**
     * Take ownership of a strand and wait for it to exit.
     *
     * If the current strand is terminated, the adopted strand is also
     * terminated.
     *
     * If the adopted strand exits with an exception, the kernel's exception
     * handler is NOT invoked. It is the caller's responsibility to handle
     * such failures.
     *
     * A strand may only be adopted one. The kernel behaviour is undefined if a
     * strand is adopted multiple times.
     *
     * By definition, this operation is COOPERATIVE.
     *
     * @param Strand $strand The strand to adopt.
     *
     * @return mixed               The value returned by the strand's entry-point coroutine.
     * @throws Throwable           The exception thrown by the strand's entry-point coroutine, if any.
     * @throws TerminatedException The adopted strand was terminated.
     */
    public static function adopt(Strand $strand)
    {
        return new ApiCall(__FUNCTION__, $strand);
    }

    /**
     * Execute multiple coroutines concurrently and wait for them all to return.
     *
     * The coroutines can be a generator objects, generator functions, or any
     * dispatchable value as documented above.
     *
     * Each coroutine is executed on its own strand. If any one of the strands
     * fails, all remaining strands are terminated and the failed strand's
     * exception is thrown.
     *
     * Otherwise, the return value is an associative array containing the return
     * values of each coroutine.
     *
     * The array keys correspond to the order that the coroutines are passed to
     * the operation. The order of the elements in the array matches the order
     * in which the strands exited. This allows predictable unpacking of the
     * array with {@see list()} (which uses the keys), while still being able to
     * tell the exit order if necessary.
     *
     * This operation is COOPERATIVE.
     *
     * @param mixed $coroutines,... The coroutines to execute.
     *
     * @return array<integer, mixed> The return values of each coroutine.
     * @throws Throwable      The exception thrown by the first failing coroutine, if any.
     */
    public static function all(...$coroutines)
    {
        return new ApiCall(__FUNCTION__, ...$coroutines);
    }

    /**
     * Execute multiple coroutines concurrently and wait for any one of them to
     * return.
     *
     * The coroutines can be a generator objects, generator functions, or any
     * dispatchable value as documented above.
     *
     * Each coroutine is executed on its own strand. If any one of the strands
     * exits without failure, all remaining strands are terminated and the
     * return value of the successful strand is returned.
     *
     * If all of the strands fail, a {@see CompositeException} is thrown.
     *
     * This operation is COOPERATIVE.
     *
     * @param mixed $coroutines,... The coroutines to execute.
     *
     * @return mixed              The return value of the first successful coroutine.
     * @throws CompositeException A container of the exceptions thrown by all of the coroutines.
     */
    public static function any(...$coroutines)
    {
        return new ApiCall(__FUNCTION__, ...$coroutines);
    }

    /**
     * Execute multiple coroutines concurrently and wait for a subset of them to
     * return.
     *
     * The coroutines can be a generator objects, generator functions, or any
     * dispatchable value as documented above.
     *
     * Each coroutine is executed on its own strand. Once the specified number
     * of strands have succeeded, all remaining strands are terminated and an
     * associative array containing the return values of each successful
     * coroutine is returned.
     *
     * The array keys correspond to the order that the coroutines are passed to
     * the operation. The order of the elements in the array matches the order
     * in which the strands exited.
     *
     * Unlike {@see Recoil::all()}, {@see list()} can not be used to unpack the
     * result directly, as the caller can not predict which of the strands will
     * succeed.
     *
     * If enough strands fail, such that is no longer possible for the required
     * number of strands to succeed, all remaining strands are terminated and
     * a {@see CompositeException} is thrown.
     *
     * The specified count must be between 1 and the number of provided
     * coroutines, inclusive.
     *
     * This operation is COOPERATIVE.
     *
     * @param int   $count          The required number of successful strands.
     * @param mixed $coroutines,... The coroutines to execute.
     *
     * @return array<mixed>       The return values of the successful coroutines.
     * @throws CompositeException A container of the exceptions thrown by the failed coroutines.
     */
    public static function some(int $count, ...$coroutines)
    {
        return new ApiCall(__FUNCTION__, $count, ...$coroutines);
    }

    /**
     * Execute multiple coroutines concurrently and wait for any one of them
     * to return or throw an exception.
     *
     * The coroutines can be a generator objects, generator functions, or any
     * dispatchable value as documented above.
     *
     * Each coroutine is executed on its own strand. When any one of the strands
     * exits, all remaining strands are terminated and the return value or
     * thrown exception is return/thrown from this method.
     *
     * This operation is COOPERATIVE.
     *
     * @param mixed $coroutines,... The coroutines to execute.
     *
     * @return mixed     The return value of the first coroutine to return.
     * @throws Throwable The exception thrown by the first coroutine to throw, if any.
     */
    public static function first(...$coroutines)
    {
        return new ApiCall(__FUNCTION__, ...$coroutines);
    }

    /**
     * Read data from a stream.
     *
     * A read buffer is filled with incoming data until its length falls between
     * $minLength and $maxLength, inclusive, or there is no more data to read.
     * $minLength and $maxLength may be equal to fill a fixed-size buffer.
     *
     * Multiple strands may read a single strand. Reads are synchronized such
     * that one strand's read operation returns before another strand begins
     * filling its read buffer. There is no guarantee as to the order in which
     * strands are granted access to the stream.
     *
     * This operation is COOPERATIVE.
     *
     * It is assumed that the stream is opened for reading and configured as
     * non-blocking.
     *
     * @see stream_set_blocking()
     *
     * @param resource $stream    A readable stream resource.
     * @param int      $minLength The minimum number of bytes to read.
     * @param int      $maxLength The maximum number of bytes to read.
     *
     * @return string The data read from the stream.
     */
    public static function read(
        $stream,
        int $minLength = PHP_INT_MAX,
        int $maxLength = PHP_INT_MAX
    ) {
        return new ApiCall(__FUNCTION__, $stream, $minLength, $maxLength);
    }

    /**
     * Write data to a stream.
     *
     * Data from $buffer is written to the stream until $length bytes have been
     * sent, or the buffer is exhausted.
     *
     * Multiple strands may write to a single strand. Writes are synchronized
     * such that one strand's write operation returns before another strand
     * begins sending. There is no guarantee as to the order in which strands
     * are granted access to the stream.
     *
     * This operation is COOPERATIVE.
     *
     * It is assumed that the stream is opened for writing and configured as
     * non-blocking.
     *
     * @see stream_set_blocking()
     *
     * @param resource $stream A writable stream resource.
     * @param string   $buffer The data to write to the stream.
     * @param int      $length The maximum number of bytes to write.
     */
    public static function write(
        $stream,
        string $buffer,
        int $length = PHP_INT_MAX
    ) {
        return new ApiCall(__FUNCTION__, $stream, $buffer, $length);
    }

    /**
     * Invoke a non-standard API oepration.
     *
     * @param string $name      The operation name.
     * @param array  $arguments The operation arguments.
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return new ApiCall($name, ...$arguments);
    }
}
