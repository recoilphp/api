<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Recoil;

use Exception;
use PHPUnit\Framework\TestCase;
use Recoil\Exception\RecoilException;
use ReflectionClass;
use ReflectionType;

/**
 * This test performs a rather basic static analysis of all interfaces to verify
 * that all type-hints are valid and reference other interfaces in the API or
 * a small set of approved dependencies / built-in types.
 */
class SanityChecksTest extends TestCase
{
    /**
     * @dataProvider exceptions
     */
    public function testExceptions($class) {
        $reflector = new ReflectionClass($class);

        $this->assertTrue($reflector->isSubClassOf(Exception::class));
        $this->assertTrue($reflector->implementsInterface(RecoilException::class));

        $this->assertTrue($reflector->hasMethod('create'));

        $method = $reflector->getMethod('create');
        $this->assertTrue($method->isPublic());
        $this->assertTrue($method->isStatic());

        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('self', (string) $returnType);
    }

    public function exceptions(): array {
        return [
            [\Recoil\Exception\CompositeException::class],
            [\Recoil\Exception\KernelException::class],
            [\Recoil\Exception\StrandException::class],
            [\Recoil\Exception\TerminatedException::class],
            [\Recoil\Exception\TimeoutException::class],
        ];
    }

    /**
     * @dataProvider interfaces
     */
    public function testInterfaces($class) {
        $reflector = new ReflectionClass($class);

        $this->assertTrue($reflector->isInterface());

        foreach ($reflector->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() !== $reflector->getName()) {
                continue;
            }

            foreach ($method->getParameters() as $parameter) {
                $this->checkTypeHint($parameter->getType());
            }

            $this->checkTypeHint($method->getReturnType());
        }
    }

    public function interfaces(): array {
        return [
            [\Recoil\Exception\PanicException::class],
            [\Recoil\Exception\RecoilException::class],

            [\Recoil\Awaitable::class],
            [\Recoil\AwaitableProvider::class],
            [\Recoil\CoroutineProvider::class],
            [\Recoil\Kernel::class],
            [\Recoil\Listener::class],
            [\Recoil\Strand::class],
            [\Recoil\StrandTrace::class],
        ];
    }

    public function whitelist(): array {
        $whitelist = [
            'Generator',
            'Throwable',
        ];

        foreach ($this->exceptions() as $parameters) {
            $whitelist[] = $parameters[0];
        }

        foreach ($this->interfaces() as $parameters) {
            $whitelist[] = $parameters[0];
        }

        return $whitelist;
    }

    private function checkTypeHint(?ReflectionType $type) {
        if ($type === null) {
            return;
        } elseif ($type->isBuiltin()) {
            return;
        }

        $type = ltrim((string) $type, '?');

        $this->assertContains($type, $this->whitelist());
        $this->assertTrue(
            interface_exists($type) || class_exists($type),
        );
    }
}
