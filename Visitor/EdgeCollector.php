<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Graph;
use Trismegiste\Mondrian\Transform\Context;
use Trismegiste\Mondrian\Transform\CompilerPass;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;

/**
 * EdgeCollector is a visitor to transform code into graph edges
 *
 * This class is too long. I'll refactor it when I'll find what pattern is fitted
 */
class EdgeCollector extends \PHPParser_NodeVisitor_NameResolver implements CompilerPass
{

    protected $currentClass = false;
    protected $currentClassVertex = null;
    protected $currentMethod = false;
    protected $currentMethodNode = null;
    protected $graph;
    protected $vertex;
    protected $inheritanceMap;

    public function __construct(Context $ctx)
    {
        $this->graph = $ctx->graph;
        $this->vertex = &$ctx->vertex;
        $this->inheritanceMap = &$ctx->inheritanceMap;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        switch ($node->getType()) {

            case 'Stmt_Class' :
                $this->enterClassNode($node);
                break;

            case 'Stmt_Interface' :
                $this->enterInterfaceNode($node);
                break;

            case 'Stmt_ClassMethod' :
                if ($node->isPublic()) {
                    $this->enterMethodNode($node);
                }
                break;

            case 'Expr_MethodCall' :
                // since we only track the public method, we check we are in :
                if ($this->currentMethod) {
                    $this->enterMethodCall($node);
                }
                break;

            case 'Expr_New':
                // since we only track the public method, we check we are in :
                if ($this->currentMethod) {
                    $this->enterNewInstance($node);
                }
                break;
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_Class':
            case 'Stmt_Interface';
                $this->currentClass = false;
                $this->currentClassVertex = null;
                break;

            case 'Stmt_ClassMethod' :
                $this->currentMethod = false;
                $this->currentMethodNode = null;
                break;
        }
    }

    /**
     * Find a vertex by its type and name
     *
     * @param string $type
     * @param string $key
     * @return Vertex or null
     */
    protected function findVertex($type, $key)
    {
        if (array_key_exists($key, $this->vertex[$type])) {
            return $this->vertex[$type][$key];
        }
        return null;
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
     * Search if a type (class or interface) exists in the inheritanceMap
     *
     * @param string $cls
     * @return bool
     */
    protected function hasDeclaringClass($cls)
    {
        return array_key_exists($cls, $this->inheritanceMap);
    }

    /**
     * Finds the FQCN of the first declaring class/interface of a method
     *
     * @param string $cls subclass name
     * @param string $meth method name
     * @return string
     */
    protected function getDeclaringClass($cls, $meth)
    {
        return $this->inheritanceMap[$cls]['method'][$meth];
    }

    /**
     * Is FQCN an interface ?
     *
     * @param string $cls FQCN
     * @return bool
     */
    protected function isInterface($cls)
    {
        return $this->inheritanceMap[$cls]['interface'];
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
                // we clone because resolveClassName has edge effect
                $tmp = clone $param->type;
                $paramType = (string) $this->resolveClassName($tmp);
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
     * Visits a method node
     * Be warned : currently a lava flow
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     */
    protected function enterMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $this->currentMethod = $node->name;
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
     * Visits an interface node
     *
     * @param \PHPParser_Node_Stmt_Interface $node
     */
    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        $this->currentClass = (string) $node->namespacedName;
        $src = $this->vertex['interface'][$this->currentClass];
        $this->currentClassVertex = $src;

        // implements
        foreach ($node->extends as $interf) {
            if (null !== $dst = $this->findVertex('interface', (string) $interf)) {
                $this->graph->addEdge($src, $dst);
            }
        }
    }

    /**
     * Visits a class node
     *
     * @param \PHPParser_Node_Stmt_Class $node
     */
    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->currentClass = (string) $node->namespacedName;
        $src = $this->vertex['class'][$this->currentClass];
        $this->currentClassVertex = $src;

        // extends
        if (!is_null($node->extends)) {
            if (null !== $dst = $this->findVertex('class', (string) $node->extends)) {
                $this->graph->addEdge($src, $dst);
            }
        }
        // implements
        foreach ($node->implements as $interf) {
            if (null !== $dst = $this->findVertex('interface', (string) $interf)) {
                $this->graph->addEdge($src, $dst);
            }
        }
    }

    public function compile()
    {
        // nothing to do
    }

    /**
     * Links the current implementation vertex to all methods with the same
     * name. Filters on some obvious cases.
     * Be warned : Lava Flow
     *
     * @param \PHPParser_Node_Expr_MethodCall $node
     * @return void
     *
     */
    protected function enterMethodCall(\PHPParser_Node_Expr_MethodCall $node)
    {
        $method = $node->name;
        if (is_string($method)) {
            // skipping some obvious calls :
            if (($node->var->getType() == 'Expr_Variable')
                    && (is_string($node->var->name))) {
                $called = $node->var->name;
                // skipping $this :
                if ($called == 'this') {
                    return;
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
                    if ($param->type instanceof \PHPParser_Node_Name_FullyQualified) {
                        $paramType = (string) $param->type;
                        // we check if it is an outer class or not : is it known ?
                        if ($this->hasDeclaringClass($paramType)) {
                            $cls = $this->getDeclaringClass($paramType, $method);
                            if (!is_null($signature = $this->findVertex('method', "$cls::$method"))) {
                                $candidate = array($signature);
                            }
                        }
                    }
                }
            }
            // fallback : link to every methods with the same name :
            if (!isset($candidate)) {
                $candidate = array_filter($this->vertex['method'], function($val) use ($method) {
                            return preg_match("#::$method$#", $val->getName());
                        });
            }
            $impl = $this->findVertex('impl', $this->currentClass . '::' . $this->currentMethod);
            foreach ($candidate as $methodVertex) {
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
        if ($node->class instanceof \PHPParser_Node_Name_FullyQualified) {
            $classVertex = $this->findVertex('class', (string) $node->class);
            if (!is_null($classVertex)) {
                $impl = $this->findVertex('impl', $this->currentClass . '::' . $this->currentMethod);
                $this->graph->addEdge($impl, $classVertex);
            }
        }
    }

}