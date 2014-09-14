<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * FileLevel is ...
 */
class FileLevel extends AbstractState
{

    public function enter(Node $node)
    {
        switch ($node->getType()) {
            case 'Stmt_Namespace':
                // ...
                break;
            case 'Stmt_UseUse':
                break;
            case 'Stmt_Class':
                $this->context->pushState('class', $node);
                break;
            case 'Stmt_Trait':
                $this->context->pushState('trait', $node);
                break;
            case 'Stmt_Interface':
                $this->context->pushState('interface', $node);
                break;
        }
    }

    public function getName()
    {
        return 'file';
    }

    public function leave(Node $node)
    {
        
    }

}