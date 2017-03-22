<?php

declare(strict_types = 1);

namespace Marcosh\PhpReturnTypeChecker\Anomaly;

use Roave\BetterReflection\Reflection\ReflectionMethod;

final class MissingMethodReturnTypeWithDocBlock implements Anomaly
{
    /**
     * @var ReflectionMethod
     */
    private $method;

    private function __construct(ReflectionMethod $method)
    {
        $this->method = $method;
    }

    public static function method(ReflectionMethod $method): \Iterator
    {
        try {
            $docBlockReturnTypes = $method->getDocBlockReturnTypes();
        } catch (\InvalidArgumentException $e) {
            // we need this here to prevent reflection-bocblock errors on @see invalid Fqsen
        }

        if (null === $method->getReturnType() && !empty($docBlockReturnTypes)) {
            yield new self($method);
        }
    }

    public function message(): string
    {
        try {
            $docBlockReturnTypes = $this->method->getDocBlockReturnTypes();
        } catch (\InvalidArgumentException $e) {
            // we need this here to prevent reflection-bocblock errors on @see invalid Fqsen
        }

        return sprintf(
            'Method <info>%s</info> of the class <info>%s</info> defined in <comment>%s</comment> ' .
            'does not have a return type but has a doc block return type of <info>%s.</info>',
            $this->method->getName(),
            $this->method->getDeclaringClass()->getName(),
            $this->method->getFileName(),
            implode($docBlockReturnTypes)
        );
    }
}