<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * ClassLevel is ...
 */
class ClassLevel extends AbstractState
{

    public function enter(Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_TraitUse' :
                $this->importSignatureTrait($node);
                break;

            case 'Stmt_ClassMethod':
                if ($node->type === Node\Stmt\Class_::MODIFIER_PUBLIC) {
                    $this->context->pushState('class-method', $node);
                }
                break;
        }
    }

    public function getName()
    {
        return 'class';
    }

}