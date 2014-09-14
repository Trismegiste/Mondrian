<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * ClassMethodLevel is ...
 */
class ClassMethodLevel extends AbstractState
{

    public function enter(Node $node)
    {
        $currentClass = $this->context->getNodeFor('class');
        $currentMethod = $this->context->getNodeFor('class-method');

        switch ($node->getType()) {
            case 'Expr_MethodCall':
                // ...
                break;
        }
    }

    public function getName()
    {
        return 'class-method';
    }

}