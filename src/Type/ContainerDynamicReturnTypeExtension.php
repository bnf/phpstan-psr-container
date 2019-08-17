<?php
declare(strict_types=1);

namespace Bnf\PhpstanPsrContainer\Type;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use Psr\Container\ContainerInterface;

class ContainerDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ContainerInterface::class;
    }

    public function isMethodSupported(MethodReflection $reflection): bool
    {
        return $reflection->getName() === 'get';
    }

    public function getTypeFromMethodCall(MethodReflection $reflection, MethodCall $methodCall, Scope $scope): Type
    {
        if (count($methodCall->args) === 0) {
            return ParametersAcceptorSelector::selectSingle($reflection->getVariants())->getReturnType();
        }
        $arg = $methodCall->args[0]->value;

        if ($arg instanceof \PhpParser\Node\Expr\ClassConstFetch) {
            return new ObjectType((string)$arg->class);
        }

        return ParametersAcceptorSelector::selectSingle($reflection->getVariants())->getReturnType();
    }
}
