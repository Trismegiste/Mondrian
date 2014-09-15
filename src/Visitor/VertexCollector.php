<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\Vertex;

/**
 * VertexCollector is a visitor to transform code into graph vertices
 */
class VertexCollector extends PassCollector
{

    /**
     * {@inheritDoc}
     */
    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $index = $this->currentClass;
        if (!$this->existsVertex('class', $index)) {
            $v = new Vertex\ClassVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('class', $index, $v);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        $index = $this->currentClass;
        if (!$this->existsVertex('interface', $index)) {
            $v = new Vertex\InterfaceVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('interface', $index, $v);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        if ($this->isTrait($this->currentClass)) {
            $this->enterTraitMethod($node);
        } elseif ($this->isInterface($this->currentClass)) {
            $this->enterInterfaceMethod($node);
        } else {
            $this->enterClassMethod($node);
        }
    }

    private function enterTraitMethod(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        // create implemenation node
        // if not abstract we add the vertex for the implementation
        if (!$node->isAbstract()) {
            $this->pushImplementation($node);
        }

        // push param for implementation, these parameters will be connected 
        // to copy-pasted signature (see below)
        $index = $this->currentClass . '::' . $this->currentMethod;
        foreach ($node->params as $order => $aParam) {
            $this->pushParameter($index, $order);
        }

        // copy paste this signature in every class which use this current trait
        // Anyway we check if there is no other parent which declaring first this method
        $traitUser = $this->getClassesUsingTraitForDeclaringMethod($this->currentClass, $this->currentMethod);
        foreach ($traitUser as $classname) {
            // we copy-paste the signature declaration in the class which using the current trait
            $index = $classname . '::' . $this->currentMethod;
            if (!$this->existsVertex('method', $index)) {
                $v = new Vertex\MethodVertex($index);
                $this->graph->addVertex($v);
                $this->indicesVertex('method', $index, $v);
            }
            // we do not copy-paste the parameters, there will be connected to original parameters from trait (see above)
        }
    }

    private function enterClassMethod(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        // if this class is declaring the method, we create a vertex for this signature
        $declaringClass = $this->getDeclaringClass($this->currentClass, $this->currentMethod);
        if ($this->currentClass == $declaringClass) {
            $this->pushMethod($node);
        }

        // if not abstract we add the vertex for the implementation
        if (!$node->isAbstract()) {
            $this->pushImplementation($node);
        }
    }

    private function enterInterfaceMethod(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        // if this interface is declaring the method, we create a vertex for this signature
        $declaringClass = $this->getDeclaringClass($this->currentClass, $this->currentMethod);
        if ($this->currentClass == $declaringClass) {
            $this->pushMethod($node);
        }
    }

    /**
     * Adding a new vertex if the method is not already indexed
     * Since it is a method, I'm also adding the parameters
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     */
    protected function pushMethod(\PHPParser_Node_Stmt_ClassMethod $node, $index = null)
    {
        if (is_null($index)) {
            $index = $this->getCurrentMethodIndex();
        }
        if (!$this->existsVertex('method', $index)) {
            $v = new Vertex\MethodVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('method', $index, $v);
            // now param
            foreach ($node->params as $order => $aParam) {
                $this->pushParameter($index, $order);
            }
        }
    }

    /**
     * Adding a new vertex if the implementation is not already indexed
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     */
    protected function pushImplementation(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $index = $this->getCurrentMethodIndex();
        if (!$this->existsVertex('impl', $index)) {
            $v = new Vertex\ImplVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('impl', $index, $v);
        }
    }

    /**
     * Add a parameter vertex. I must point out that I store the order
     * of the parameter, not its name. Why ? Because, name can change accross
     * inheritance tree. Therefore, it could fail the refactoring of the source
     * from the digraph.
     *
     * @param string $methodName like 'FQCN::method'
     * @param int $order
     */
    protected function pushParameter($methodName, $order)
    {
        $index = $methodName . '/' . $order;
        if (!$this->existsVertex('param', $index)) {
            $v = new Vertex\ParamVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('param', $index, $v);
        }
    }

    protected function enterTraitNode(\PHPParser_Node_Stmt_Trait $node)
    {
        $index = $this->currentClass;
        if (!$this->existsVertex('trait', $index)) {
            $v = new Vertex\TraitVertex($index);
            $this->graph->addVertex($v);
            $this->indicesVertex('trait', $index, $v);
        }
    }

}
