<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use PhpParser\Node;

/**
 * ClassMethodLevel is ...
 */
class ClassMethodLevel extends MethodLevelHelper
{

    private $fileState;
    private $currentFqcn;
    private $currentMethodNode;

    public function enter(Node $node)
    {
        $this->currentFqcn = $this->context->getState('file')->getNamespacedName($this->context->getNodeFor('class'));
        $this->currentMethodNode = $this->context->getNodeFor($this->getName());
        $this->fileState = $this->context->getState('file');

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

    public function getName()
    {
        return 'class-method';
    }

    /**
     * Links the current implementation vertex to all methods with the same
     * name. Filters on some obvious cases.
     *
     * @param \PHPParser_Node_Expr_MethodCall $node
     * @return void
     *
     */
    protected function enterMethodCall(Node\Expr\MethodCall $node)
    {
        if (is_string($node->name)) {
            $this->enterNonDynamicMethodCall($node);
        }
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
    protected function enterNonDynamicMethodCall(Node\Expr\MethodCall $node)
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
            $candidate = $this->getGraphContext()
                    ->findAllMethodSameName($method);
            if (count($candidate)) {
                // store the fallback for futher report
                foreach ($candidate as $called) {
                    $this->getGraphContext()
                            ->logFallbackCall($this->currentFqcn, $this->currentMethodNode->name, $called->getName());
                }
            }
        }
        $impl = $this->findVertex('impl', $this->currentFqcn . '::' . $this->currentMethodNode->name);
        // fallback or not, we exclude calls from annotations
        $exclude = $this->getGraphContext()
                ->getExcludedCall($this->currentFqcn, $this->currentMethodNode->name);
        foreach ($candidate as $methodVertex) {
            if (!in_array($methodVertex->getName(), $exclude)) {
                $this->getGraph()->addEdge($impl, $methodVertex);
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
                $paramType = (string) $this->fileState->resolveClassName($param->type);
                // we check if it is an outer class or not : is it known ?
                if (!is_null($cls = $this->getReflectionContext()
                                ->findMethodInInheritanceTree($paramType, $method))) {
                    if (!is_null($signature = $this->findVertex('method', "$cls::$method"))) {
                        return array($signature);
                    }
                }
            }
        }

        return null;  // can't see shit captain
    }

}