<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use Trismegiste\Mondrian\Visitor\State\FileLevelTemplate;
use PhpParser\Node\Stmt;

/**
 * FileLevel is ...
 */
class FileLevel extends FileLevelTemplate
{

    protected function enterClassNode(Stmt\Class_ $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $src = $this->findVertex('class', $fqcn);

        // extends
        if (!is_null($node->extends)) {
            if (null !== $dst = $this->findVertex('class', (string) $this->resolveClassName($node->extends))) {
                $this->getGraph()->addEdge($src, $dst);
            }
        }
        // implements
        foreach ($node->implements as $interf) {
            if (null !== $dst = $this->findVertex('interface', (string) $this->resolveClassName($interf))) {
                $this->getGraph()->addEdge($src, $dst);
            }
        }
    }

    protected function enterInterfaceNode(Stmt\Interface_ $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $src = $this->findVertex('interface', $fqcn);

        // implements
        foreach ($node->extends as $interf) {
            if (null !== $dst = $this->findVertex('interface', (string) $this->resolveClassName($interf))) {
                $this->getGraph()->addEdge($src, $dst);
            }
        }
    }

    protected function enterTraitNode(Stmt\Trait_ $node)
    {
        
    }

}