<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Vertex;

use PhpParser\Node;

/**
 * ClassLevel is ...
 */
class ClassLevel extends ObjectLevelHelper
{

    public function enter(Node $node)
    {
        if (($node->getType() == 'Stmt_ClassMethod') &&
                ($node->isPublic())) {
            $fqcn = $this->getCurrentFqcn();
            // if this class is declaring the method, we create a vertex for this signature
            $declaringClass = $this->getReflectionContext()->getDeclaringClass($fqcn, $node->name);
            if ($this->currentClass == $declaringClass) {
                $this->pushMethod($node);
            }

            // if not abstract we add the vertex for the implementation
            if (!$node->isAbstract()) {
                $this->pushImplementation($node);
            }
        }
    }

    public function getName()
    {
        return 'class';
    }

}