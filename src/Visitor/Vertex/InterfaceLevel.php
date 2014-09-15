<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Vertex;

use PhpParser\Node;

/**
 * InterfaceLevel is ...
 */
class InterfaceLevel extends ObjectLevelHelper
{

    public function enter(Node $node)
    {
        if (($node->getType() == 'Stmt_ClassMethod') &&
                $node->isPublic()) {
            $methodName = $node->name;
            $fqcn = $this->getCurrentFqcn();
            // if this class is declaring the method, we create a vertex for this signature
            $declaringClass = $this->getReflectionContext()->getDeclaringClass($fqcn, $methodName);
            if ($fqcn == $declaringClass) {
                $this->pushMethod($node, "$fqcn::$methodName");
            }
        }
    }

    public function getName()
    {
        return 'interface';
    }

}