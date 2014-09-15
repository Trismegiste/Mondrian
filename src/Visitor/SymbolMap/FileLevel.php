<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\SymbolMap;

use PhpParser\Node;
use Trismegiste\Mondrian\Transform\ReflectionContext;
use Trismegiste\Mondrian\Visitor\State\FileLevelTemplate;

/**
 * FileLevel is ...
 */
class FileLevel extends FileLevelTemplate
{

    protected function enterClassNode(Node\Stmt\Class_ $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $this->context->getReflectionContext()->initSymbol($fqcn, ReflectionContext::SYMBOL_CLASS);
        // extends
        if (!is_null($node->extends)) {
            $name = (string) $this->resolveClassName($node->extends);
            $this->context->getReflectionContext()->initSymbol($name, ReflectionContext::SYMBOL_CLASS);
            $this->context->getReflectionContext()->pushParentClass($fqcn, $name);
        }
        // implements
        foreach ($node->implements as $parent) {
            $name = (string) $this->resolveClassName($parent);
            $this->context->getReflectionContext()->initSymbol($name, ReflectionContext::SYMBOL_INTERFACE);
            $this->context->getReflectionContext()->pushParentClass($fqcn, $name);
        }
    }

    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $this->context->getReflectionContext()->initSymbol($fqcn, ReflectionContext::SYMBOL_INTERFACE);
        // extends
        foreach ($node->extends as $interf) {
            $name = (string) $this->resolveClassName($interf);
            $this->context->getReflectionContext()->initSymbol($name, ReflectionContext::SYMBOL_INTERFACE);
            $this->context->getReflectionContext()->pushParentClass($fqcn, $name);
        }
    }

    protected function enterTraitNode(\PHPParser_Node_Stmt_Trait $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $this->context->getReflectionContext()->initSymbol($fqcn, ReflectionContext::SYMBOL_TRAIT);
    }

}