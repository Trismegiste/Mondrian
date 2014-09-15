<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * ClassLevel is ...
 */
class ClassLevel extends TraitUserLevel
{

    public function enter(Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_TraitUse' :
                $this->importSignatureTrait($node);
                break;

            case 'Stmt_ClassMethod':
                if ($node->isPublic()) {
                    $fqcn = $this->getCurrentFqcn();
                    $this->getReflectionContext()->addMethodToClass($fqcn, $node->name);
                }
                break;
        }
    }

    public function getName()
    {
        return 'class';
    }

}