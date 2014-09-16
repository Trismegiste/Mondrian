<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Vertex;

use PhpParser\Node\Stmt;

/**
 * ClassLevel is ...
 */
class ClassLevel extends ObjectLevelHelper
{

    protected function enterPublicMethod($fqcn, Stmt\ClassMethod $node)
    {
        $methodName = $node->name;
        // if this class is declaring the method, we create a vertex for this signature
        $declaringClass = $this->getReflectionContext()->getDeclaringClass($fqcn, $methodName);
        if ($fqcn == $declaringClass) {
            $this->pushMethod($node, "$fqcn::$methodName");
        }

        // if not abstract we add the vertex for the implementation
        if (!$node->isAbstract()) {
            $this->pushImplementation($node, "$fqcn::$methodName");
        }
    }

    public function getName()
    {
        return 'class';
    }

}