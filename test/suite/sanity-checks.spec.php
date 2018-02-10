<?php

declare(strict_types=1); // @codeCoverageIgnore

use Recoil\Exception\RecoilException;

/*
 * This test performs a rather basic static analysis of all interfaces to verify
 * that all type-hints are valid and reference other interfaces in the API or
 * a small set of approved dependencies / built-in types.
 */
context('sanity checks', function () {
    $this->exceptions = [
        Recoil\Exception\CompositeException::class,
        Recoil\Exception\KernelException::class,
        Recoil\Exception\StrandException::class,
        Recoil\Exception\TerminatedException::class,
        Recoil\Exception\TimeoutException::class,
    ];

    $this->interfaces = [
        Recoil\Exception\PanicException::class,
        Recoil\Exception\RecoilException::class,

        Recoil\Awaitable::class,
        Recoil\AwaitableProvider::class,
        Recoil\CoroutineProvider::class,
        Recoil\Kernel::class,
        Recoil\Listener::class,
        Recoil\Strand::class,
        Recoil\StrandTrace::class,
    ];

    $this->whitelist = array_merge(
        $this->exceptions,
        $this->interfaces,
        [
            'Generator',
            'Throwable',
        ]
    );

    $this->checkTypeHint = function (ReflectionType $type = null) {
        if ($type === null) {
            return;
        } elseif ($type->isBuiltin()) {
            return;
        }

        $type = (string) $type;
        expect($this->whitelist)->to->contain($type);
        expect(interface_exists($type) || class_exists($type))->to->be->true;
    };

    context('exception definitions', function () {
        foreach ($this->exceptions as $exception) {
            describe($exception, function () use ($exception) {
                $reflector = new ReflectionClass($exception);

                it('is an exception', function () use ($reflector) {
                    expect($reflector->isSubclassOf(Exception::class))->to->be->true;
                });

                it('is a recoil exception', function () use ($reflector) {
                    expect($reflector->implementsInterface(RecoilException::class))->to->be->true;
                });

                it('has a static create() method', function () use ($reflector) {
                    expect($reflector->hasMethod('create'))->to->be->true;

                    $method = $reflector->getMethod('create');
                    expect($method->isPublic())->to->be->true;
                    expect($method->isStatic())->to->be->true;

                    $returnType = $method->getReturnType();
                    expect($returnType)->not->to->be->null;
                    expect((string) $returnType)->to->equal('self');
                });
            });
        }
    });

    context('interface definitions', function () {
        foreach ($this->interfaces as $interface) {
            describe($interface, function () use ($interface) {
                $reflector = new ReflectionClass($interface);

                it('is an interface', function () use ($reflector) {
                    expect($reflector->isInterface())->to->be->true;
                });

                foreach ($reflector->getMethods() as $method) {
                    if ($method->getDeclaringClass()->getName() !== $reflector->getName()) {
                        continue;
                    }

                    describe(
                        '->' . $method->getName() . '()',
                        function () use ($method) {
                            foreach ($method->getParameters() as $parameter) {
                                it(
                                    'has a valid type hint for $' . $parameter->getName(),
                                    function () use ($parameter) {
                                        ($this->checkTypeHint)($parameter->getType());
                                    }
                                );
                            }

                            it('has a valid return type hint', function () use ($method) {
                                ($this->checkTypeHint)($method->getReturnType());
                            });
                        }
                    );
                }
            });
        }
    });
});
