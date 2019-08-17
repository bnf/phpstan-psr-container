<?php
declare(strict_types=1);

namespace Bnf\PhpstanPsrContainer;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
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
        // Care only for ::class parameters, we can not guess types for random strings.
        if (!$arg instanceof \PhpParser\Node\Expr\ClassConstFetch) {
            return ParametersAcceptorSelector::selectSingle($reflection->getVariants())->getReturnType();
        }

        $argType = $scope->getType($methodCall->args[0]->value);
        if (!$argType instanceof ConstantStringType) {
            return ParametersAcceptorSelector::selectSingle($reflection->getVariants())->getReturnType();
        }

        return new ObjectType($argType->getValue());
    }
}
