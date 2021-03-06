<?php

declare(strict_types = 1);

namespace Marcosh\PhpTypeChecker\Anomaly;

use Marcosh\PhpTypeChecker\Check\FunctionAbstract;
use Roave\BetterReflection\Reflection\ReflectionFunction;

final class FunctionReturnTypeDoesNotCoincideWithDocBlock implements Anomaly
{
    /**
     * @var ReflectionFunction
     */
    private $function;

    private function __construct(ReflectionFunction $function)
    {
        $this->function = $function;
    }

    public static function function(ReflectionFunction $function): \Iterator
    {
        if ((new FunctionAbstract($function))->returnTypeDoesNotCoincideWithDocBlock()) {
            yield new self($function);
        }
    }

    public function message(): string
    {
        try {
            $docBlockReturnTypes = $this->function->getDocBlockReturnTypes();
        } catch (\InvalidArgumentException $e) {
            // we need this here to prevent reflection-bocblock errors on @see invalid Fqsen
        }

        return sprintf(
            'Function <info>%s</info> defined ' .
            'in <comment>%s</comment> has a <info>%s</info> return type, ' .
            'but has a <info>%s</info> return doc block.',
            $this->function->getName(),
            $this->function->getFileName(),
            $this->function->getReturnType(),
            implode($docBlockReturnTypes)
        );
    }
}
