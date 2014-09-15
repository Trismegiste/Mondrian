<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * InterfaceLevel is ...
 */
class InterfaceLevel extends AbstractState
{

    public function enter(Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_ClassMethod':
                if ($node->isPublic()) {
                    $classNode = $this->context->getNodeFor('interface');
                    $fileState = $this->context->getState('file');
                    $fqcn = $fileState->getNamespacedName($classNode);
                    $this->context->getReflectionContext()->addMethodToClass($fqcn, $node->name);
                }
                break;
        }
    }

    public function getName()
    {
        return 'interface';
    }

}