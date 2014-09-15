<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Vertex;

use PhpParser\Node\Stmt;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;

/**
 * TraitLevel is ...
 */
class TraitLevel extends ObjectLevelHelper
{

    public function getName()
    {
        return 'trait';
    }

    protected function enterPublicMethod($fqcn, Stmt\ClassMethod $node)
    {
        $methodName = $node->name;
        $index = "$fqcn::$methodName";
        // create implemenation node
        // if not abstract we add the vertex for the implementation
        if (!$node->isAbstract()) {
            $this->pushImplementation($node, $index);
        }

        // push param for implementation, these parameters will be connected 
        // to copy-pasted signature (see below)
        foreach ($node->params as $order => $aParam) {
            $this->pushParameter($index, $order);
        }

        // copy paste this signature in every class which use this current trait
        // Anyway we check if there is no other parent which declaring first this method
        $traitUser = $this->getReflectionContext()->getClassesUsingTraitForDeclaringMethod($fqcn, $methodName);
        foreach ($traitUser as $classname) {
            // we copy-paste the signature declaration in the class which using the current trait
            $index = $classname . '::' . $methodName;
            if (!$this->getGraphContext()->existsVertex('method', $index)) {
                $v = new MethodVertex($index);
                $this->getGraph()->addVertex($v);
                $this->getGraphContext()->indicesVertex('method', $index, $v);
            }
            // we do not copy-paste the parameters, there will be connected to original parameters from trait (see above)
        }
    }

}