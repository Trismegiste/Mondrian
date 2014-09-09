<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;

/**
 * EdgeCollector is a visitor to transform code into graph edges
 *
 * This class is too long. I'll refactor it when I'll find what pattern is fitted
 */
class EdgeCollector extends PassCollector
{

    protected $currentClassVertex = null;
    protected $currentMethodNode = null;

    /**
     * {@inheritDoc}
     */
    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        // since we only track the public method, we check we are in :
        if ($this->currentMethod) {

            switch ($node->getType()) {

                case 'Expr_MethodCall' :
                    $this->enterMethodCall($node);
                    break;

                case 'Expr_New':
                    $this->enterNewInstance($node);
                    break;

                case 'Expr_StaticCall':
                    $this->enterStaticCall($node);
                    break;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function leaveNode(\PHPParser_Node $node)
    {
        parent::leaveNode($node);

        switch ($node->getType()) {

            case 'Stmt_Class':
            case 'Stmt_Interface':
            case 'Stmt_Trait':
                $this->currentClassVertex = null;
                break;

            case 'Stmt_ClassMethod' :
                $this->currentMethodNode = null;
                break;
        }
    }

    /**
     * Find a ParamVertex by its [classname x mehodName x position]
     * @param string $className
     * @param string $methodName
     * @param int $idx
     * @return ParamVertex
     */
    protected function findParamVertexIdx($className, $methodName, $idx)
    {
        return $this->findVertex('param', $className . '::' . $methodName . '/' . $idx);
    }

    /**
     * Find a class or interface
     *
     * @param string $type fqcn to be found
     * @return Vertex
     */
    protected function findTypeVertex($type)
    {
        foreach (array('class', 'interface') as $pool) {
            $typeVertex = $this->findVertex($pool, $type);
            if (!is_null($typeVertex)) {
                return $typeVertex;
            }
        }

        return null;
    }

    /**
     * Process the method node and adding the vertex of the first declared method
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     * @param \Trismegiste\Mondrian\Transform\Vertex\MethodVertex $signature
     */
    protected function enterDeclaredMethodNode(\PHPParser_Node_Stmt_ClassMethod $node, MethodVertex $signature)
    {
        $this->graph->addEdge($this->currentClassVertex, $signature);
        // managing params of the signature :
        foreach ($node->params as $idx => $param) {
            // adding edge from signature to param :
            $paramVertex = $this->findParamVertexIdx($this->currentClass, $this->currentMethod, $idx);
            $this->graph->addEdge($signature, $paramVertex);
            // now the type of the param :
            if ($param->type instanceof \PHPParser_Node_Name) {
                $paramType = (string) $this->resolveClassName($param->type);
                // there is a type, we add a link to the type, if it is found
                $typeVertex = $this->findTypeVertex($paramType);
                if (!is_null($typeVertex)) {
                    // we add the edge
                    $this->graph->addEdge($paramVertex, $typeVertex);
                }
            }
        }
    }

    /**
     * Process the implementation vertex with the method node
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     * @param MethodVertex|null $signature the first declaring method vertex
     * @param string $declaringClass the first declaring class of this method
     */
    protected function enterImplementationNode(\PHPParser_Node_Stmt_ClassMethod $node, $signature, $declaringClass)
    {
        $impl = $this->findVertex('impl', $this->currentClass . '::' . $node->name);
        $this->graph->addEdge($impl, $this->currentClassVertex);
        // who is embedding the impl ?
        if ($declaringClass == $this->currentClass) {
            $this->graph->addEdge($signature, $impl);
        } else {
            $this->graph->addEdge($this->currentClassVertex, $impl);
        }
        // in any case, we link the implementation to the params
        foreach ($node->params as $idx => $param) {
            // adding edge from signature to param :
            $paramVertex = $this->findParamVertexIdx($declaringClass, $this->currentMethod, $idx);
            // it is possible to not find the param because the signature
            // is external to the source code :
            if (!is_null($paramVertex)) {
                $this->graph->addEdge($impl, $paramVertex);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $this->currentMethodNode = $node;
        // search for the declaring class of this method
        $declaringClass = $this->getDeclaringClass($this->currentClass, $this->currentMethod);
        $signature = $this->findVertex('method', $declaringClass . '::' . $node->name);
        // if current class == declaring class, we add the edge
        if ($declaringClass == $this->currentClass) {
            $this->enterDeclaredMethodNode($node, $signature);
        }
        // if not abstract, the implementation depends on the class.
        // For odd reason, a method in an interface is not abstract
        // that's why, there is a double check
        if (!$this->isInterface($this->currentClass) && !$node->isAbstract()) {
            $this->enterImplementationNode($node, $signature, $declaringClass);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        $src = $this->findVertex('interface', $this->currentClass);
        $this->currentClassVertex = $src;

        // implements
        foreach ($node->extends as $interf) {
            if (null !== $dst = $this->findVertex('interface', (string) $this->resolveClassName($interf))) {
                $this->graph->addEdge($src, $dst);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $src = $this->findVertex('class', $this->currentClass);
        $this->currentClassVertex = $src;

        // extends
        if (!is_null($node->extends)) {
            if (null !== $dst = $this->findVertex('class', (string) $this->resolveClassName($node->extends))) {
                $this->graph->addEdge($src, $dst);
            }
        }
        // implements
        foreach ($node->implements as $interf) {
            if (null !== $dst = $this->findVertex('interface', (string) $this->resolveClassName($interf))) {
                $this->graph->addEdge($src, $dst);
            }
        }
    }

    /**
     * Links the current implementation vertex to all methods with the same
     * name. Filters on some obvious cases.
     *
     * @param \PHPParser_Node_Expr_MethodCall $node
     * @return void
     *
     */
    protected function enterMethodCall(\PHPParser_Node_Expr_MethodCall $node)
    {
        if (is_string($node->name)) {
            $this->enterNonDynamicMethodCall($node);
        }
    }

    protected function enterStaticCall(\PHPParser_Node_Expr_StaticCall $node)
    {
        if (($node->class instanceof \PHPParser_Node_Name) && is_string($node->name)) {
            $impl = $this->findVertex('impl', $this->getCurrentMethodIndex());
            $target = $this->findVertex('method', (string) $this->resolveClassName($node->class) . '::' . $node->name);
            if (!is_null($target)) {
                $this->graph->addEdge($impl, $target);
            }
        }
    }

    /**
     * Try to find a signature to link with the method to call and the object against to
     *
     * @param string $called
     * @param string $method
     * @return null|array null if cannot determine vertex or an array of vertices (can be empty if no call must be made)
     */
    protected function getCalledMethodVertexOn($called, $method)
    {
        // skipping $this :
        if ($called == 'this') {
            return array();  // nothing to call
        }

        // checking if the called is a method param
        $idx = false;
        foreach ($this->currentMethodNode->params as $k => $paramSign) {
            if ($paramSign->name == $called) {
                $idx = $k;
                break;
            }
        }
        if (false !== $idx) {
            $param = $this->currentMethodNode->params[$idx];
            // is it a typed param ?
            if ($param->type instanceof \PHPParser_Node_Name) {
                $paramType = (string) $this->resolveClassName($param->type);
                // we check if it is an outer class or not : is it known ?
                if (!is_null($cls = $this->findMethodInInheritanceTree($paramType, $method))) {
                    if (!is_null($signature = $this->findVertex('method', "$cls::$method"))) {
                        return array($signature);
                    }
                }
            }
        }

        return null;  // can't see shit captain
    }

    /**
     * Process of simple call of a method
     * Sample: $obj->getThing($arg);
     * Do not process : call_user_func(array($obj, 'getThing'), $arg);
     * Do not process : $reflectionMethod->invoke($obj, 'getThing', $arg);
     *
     * @param \PHPParser_Node_Expr_MethodCall $node
     * @return void
     */
    protected function enterNonDynamicMethodCall(\PHPParser_Node_Expr_MethodCall $node)
    {
        $method = $node->name;
        $candidate = null;
        // skipping some obvious calls :
        if (($node->var->getType() == 'Expr_Variable') && (is_string($node->var->name))) {
            // searching a candidate for $called::$method
            // I think there is a chain of responsibility beneath that :
            $candidate = $this->getCalledMethodVertexOn($node->var->name, $method);
        }
        // fallback : link to every methods with the same name :
        if (is_null($candidate)) {
            $candidate = $this->findAllMethodSameName($method);
            if (count($candidate)) {
                // store the fallback for futher report
                foreach ($candidate as $called) {
                    $this->logFallbackCall($this->currentClass, $this->currentMethod, $called->getName());
                }
            }
        }
        $impl = $this->findVertex('impl', $this->currentClass . '::' . $this->currentMethod);
        // fallback or not, we exclude calls from annotations
        $exclude = $this->getExcludedCall($this->currentClass, $this->currentMethod);
        foreach ($candidate as $methodVertex) {
            if (!in_array($methodVertex->getName(), $exclude)) {
                $this->graph->addEdge($impl, $methodVertex);
            }
        }
    }

    /**
     * Visits a "new" statement node
     *
     * Add an edge from current implementation to the class which a new instance
     * is created
     *
     * @param \PHPParser_Node_Expr_New $node
     */
    protected function enterNewInstance(\PHPParser_Node_Expr_New $node)
    {
        if ($node->class instanceof \PHPParser_Node_Name) {
            $classVertex = $this->findVertex('class', (string) $this->resolveClassName($node->class));
            if (!is_null($classVertex)) {
                $impl = $this->findVertex('impl', $this->getCurrentMethodIndex());
                $this->graph->addEdge($impl, $classVertex);
            }
        }
    }

    protected function enterTraitNode(\PHPParser_Node_Stmt_Trait $node)
    {
        $src = $this->findVertex('trait', $this->currentClass);
        $this->currentClassVertex = $src;
    }

}
