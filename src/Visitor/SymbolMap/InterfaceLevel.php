<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\SymbolMap;

use PhpParser\Node;
use Trismegiste\Mondrian\Visitor\State\AbstractObjectLevel;

/**
 * InterfaceLevel is ...
 */
class InterfaceLevel extends AbstractObjectLevel
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