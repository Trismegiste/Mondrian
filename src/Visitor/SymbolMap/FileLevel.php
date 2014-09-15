<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\SymbolMap;

use PhpParser\Node;
use Trismegiste\Mondrian\Visitor\State\FileLevelTemplate;

/**
 * FileLevel registers trait/interface/class in the reflection context
 */
class FileLevel extends FileLevelTemplate
{

    protected function enterClassNode(Node\Stmt\Class_ $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $this->getReflectionContext()->initClass($fqcn);
        // extends
        if (!is_null($node->extends)) {
            $name = (string) $this->resolveClassName($node->extends);
            $this->getReflectionContext()->initClass($name);
            $this->getReflectionContext()->pushParentClass($fqcn, $name);
        }
        // implements
        foreach ($node->implements as $parent) {
            $name = (string) $this->resolveClassName($parent);
            $this->getReflectionContext()->initInterface($name);
            $this->getReflectionContext()->pushParentClass($fqcn, $name);
        }
    }

    protected function enterInterfaceNode(Node\Stmt\Interface_ $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $this->getReflectionContext()->initInterface($fqcn);
        // extends
        foreach ($node->extends as $interf) {
            $name = (string) $this->resolveClassName($interf);
            $this->getReflectionContext()->initInterface($name);
            $this->getReflectionContext()->pushParentClass($fqcn, $name);
        }
    }

    protected function enterTraitNode(Node\Stmt\Trait_ $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $this->getReflectionContext()->initTrait($fqcn);
    }

}