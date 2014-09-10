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
        // we do not add sigature method from a trait
        if (!$this->isTrait($this->currentClass)) {
            // only if this method is first declared in this class
            // we add the vertex. If not, it will be a higher class/interface
            // in the inheritance hierarchy which add it.
            $declaringClass = $this->getDeclaringClass($this->currentClass, $this->currentMethod);
            if ($this->currentClass == $declaringClass) {
                $this->pushMethod($node);
            }
        } else {
            // but instead we copy paste this signature in every class which use this current trait
            // Anyway we check if there is no other parent which declaring first this method
            $traitUser = $this->getClassesUsingTraitForDeclaringMethod($this->currentClass, $this->currentMethod);
            foreach ($traitUser as $classname) {
                // we copy-paste the signature declaration in the class which using the current trait
                $this->pushMethod($node, $classname . '::' . $this->currentMethod);
            }
        }
        // if not abstract we add the vertex for the implementation
        if (!$this->isInterface($this->currentClass) && !$node->isAbstract()) {
            $this->pushImplementation($node);
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
            // @todo bug: il ne faut pas dupliquer les param pour les traits, ils sont liés à l'unique implementation
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
