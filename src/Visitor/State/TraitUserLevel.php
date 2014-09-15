<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;
use Trismegiste\Mondrian\Transform\ReflectionContext;

/**
 * TraitUserLevel is a helper for traits users (class & trait) level state
 */
abstract class TraitUserLevel extends ObjectLevel
{

    protected function importSignatureTrait(Node\Stmt\TraitUse $node)
    {
        $fileState = $this->context->getState('file');
        $fqcn = $this->getCurrentFqcn();
        // @todo do not forget aliases
        foreach ($node->traits as $import) {
            $name = (string) $fileState->resolveClassName($import);
            $this->context->getReflectionContext()->initSymbol($name, ReflectionContext::SYMBOL_TRAIT);
            $this->context->getReflectionContext()->pushUseTrait($fqcn, $name);
        }
    }

}