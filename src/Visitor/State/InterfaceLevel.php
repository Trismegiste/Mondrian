<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * InterfaceLevel is ...
 */
class InterfaceLevel extends ObjectLevel
{

    public function enter(Node $node)
    {
        switch ($node->getType()) {

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
        return 'interface';
    }

}