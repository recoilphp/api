# Recoil API Specification

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [RFC 2119](https://tools.ietf.org/html/rfc2119).

## Core Concepts and Terminology

### Generator object

An instance of the built-in [`Generator`](http://php.net/manual/en/class.generator.php)
type.

### Generator function

A function that returns a generator object.

### Coroutine

The [general definition](https://en.wikipedia.org/wiki/Coroutine) of a coroutine
is a subroutine that may be suspended and later resumed. In the context of this
document, a coroutine is a generator object that is used to implement such a
subroutine.

### Strand

A "strand of execution", analogous to a thread. Each strand has its own
call-stack. Each stack frame represents a call to a coroutine.

### Kernel

The system responsible for creating, scheduling and executing strands.

### Dispatchable values

A value yielded by a coroutine, and recognized by the kernel, that instructs the
kernel to perform a certain operation.

This specification defines a [standard set of dispatchable values](#todo).

## Declaring coroutines

    @todo

### Suspending and resuming

    @todo

## Standard dispatchable values

The kernel MUST support the dispatchable values defined in this section.

Other values MAY be recognised as dispatchable. It is RECOMMENDED that a PHP
interface is used to identify such values.

### Coroutines and generator objects

Any generator object yielded by a coroutine MUST be treated as another coroutine.
The yielded coroutine is pushed onto the current strand's call-stack and
executed.

The calling coroutine is resumed when the yielded coroutine returns a value or
throws an exception.

<!-- This is the standard way to invoke one coroutine from another. -->

### API calls

    @todo

### The `CoroutineProvider` interface

    @todo

### The `Awaitable` interface

    @todo

### The `AwaitableProvider` interface

    @todo

### `null` values

    @todo

### `integer` and `float` values

    @todo

### `array` values

    @todo

### `resource` values

    @todo

### `thennable` values

## Source code documentation conventions

Coroutine functions SHOULD have a return type hint of `Coroutine`, which is
aliased to the `Generator` type with `use Generator as Coroutine;`. This clearly
delineates coroutine functions from "regular" generator functions.

Return types and values SHOULD be described from the perspective of the caller
of the coroutine, rather than describing the generator object produced by the
function.
