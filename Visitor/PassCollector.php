<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\Context;
use Trismegiste\Mondrian\Transform\CompilerPass;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * PassCollector is an abstract compiler pass for visiting source code
 * and build the graph with the help of a Context
 * 
 * It feels like a Mediator between Context and concrete CompilerPass
 * (It is not because Context and concrete CompilerPass are not daughter
 * of a common interface)
 */
abstract class PassCollector extends \PHPParser_NodeVisitor_NameResolver implements CompilerPass
{

    protected $graph;
    protected $currentClass = false;
    protected $currentMethod = false;
    private $context;

    public function __construct(Context $ctx, Graph $g)
    {
        $this->context = $ctx;
        $this->graph = $g;
    }

    /**
     * Visits a class node
     *
     * @param \PHPParser_Node_Stmt_Class $node
     */
    abstract protected function enterClassNode(\PHPParser_Node_Stmt_Class $node);

    /**
     * Visits an interface node
     *
     * @param \PHPParser_Node_Stmt_Interface $node
     */
    abstract protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node);

    /**
     * Visits a public method node
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     */
    abstract protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node);

    /**
     * {@inheritDoc}
     */
    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        switch ($node->getType()) {

            case 'Stmt_Class' :
                $this->currentClass = (string) $node->namespacedName;
                $this->enterClassNode($node);
                break;

            case 'Stmt_Interface' :
                $this->currentClass = (string) $node->namespacedName;
                $this->enterInterfaceNode($node);
                break;

            case 'Stmt_ClassMethod' :
                if ($node->isPublic()) {
                    $this->currentMethod = $node->name;
                    $this->enterPublicMethodNode($node);
                }
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function leaveNode(\PHPParser_Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_Class':
            case 'Stmt_Interface';
                $this->currentClass = false;
                break;

            case 'Stmt_ClassMethod' :
                $this->currentMethod = false;
                break;
        }
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
        return $this->context->getDeclaringClass($cls, $meth);
    }

    /**
     * Is FQCN an interface ?
     *
     * @param string $cls FQCN
     * @return bool
     */
    protected function isInterface($cls)
    {
        return $this->context->isInterface($cls);
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
        return $this->context->findVertex($type, $key);
    }

    /**
     * the vertex name for a MethodVertex
     *
     * @return string
     */
    protected function getCurrentMethodIndex()
    {
        return $this->currentClass . '::' . $this->currentMethod;
    }

    /**
     * See Context
     */
    protected function findAllMethodSameName($method)
    {
        return $this->context->findAllMethodSameName($method);
    }

    /**
     * See Context
     */
    protected function existsVertex($type, $key)
    {
        return $this->context->existsVertex($type, $key);
    }

    /**
     * {@inheritDoc}
     */
    public function compile()
    {
        // nothing to do
    }

    /**
     * Check if the class exists before searching for the 
     * declaring class of the method, because class could be unknown, outside
     * or code could be bugged
     */
    protected function findMethodInInheritanceTree($cls, $method)
    {
        if ($this->context->hasDeclaringClass($cls)) {
            return $this->context->findMethodInInheritanceTree($cls, $method);
        }

        return null;
    }

    /**
     * See Context
     */
    protected function indicesVertex($typ, $index, Vertex $v)
    {
        $this->context->indicesVertex($typ, $index, $v);
    }

    /**
     * Extracts annotation in the comment of a method and injects them in
     * attribute of the node
     * 
     * @param \PHPParser_Node_Stmt_ClassMethod $node 
     */
    protected function extractAnnotation(\PHPParser_Node_Stmt $node)
    {
        if ($node->hasAttribute('comments')) {
            $compil = array();
            foreach ($node->getAttribute('comments') as $comm) {
                preg_match_all('#^.*@mondrian\s+([\w]+)\s+([^\s]+)\s*$#m', $comm->getReformattedText(), $match);
                foreach ($match[0] as $idx => $matchedOccur) {
                    $compil[$match[1][$idx]][] = $match[2][$idx];
                }
            }
            // if there are annotations, we add them to the node
            foreach ($compil as $attr => $lst) {
                $node->setAttribute($attr, $lst);
            }
        }
    }

}