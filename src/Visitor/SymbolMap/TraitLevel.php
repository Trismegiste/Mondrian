<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\SymbolMap;

use PhpParser\Node;

/**
 * TraitLevel is ...
 */
class TraitLevel extends TraitUserLevel
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
        return 'trait';
    }

}