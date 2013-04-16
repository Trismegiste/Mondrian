<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\Context;
use Trismegiste\Mondrian\Transform\CompilerPass;

/**
 * PassCollector is an abstract compiler pass for visiting source code
 */
abstract class PassCollector extends \PHPParser_NodeVisitor_NameResolver implements CompilerPass
{

    protected $graph;
    protected $vertex;  // @todo must be removed
    protected $currentClass = false;
    protected $currentMethod = false;
    private $context; // perhaps I will make it protected when I'll remove inheritanceMap in the subclasses

    public function __construct(Context $ctx)
    {
        $this->context = $ctx;
        $this->graph = $ctx->graph;
        $this->vertex = &$ctx->vertex;
    }

    /**
     * Search if a type (class or interface) exists in the inheritanceMap
     *
     * @param string $cls
     * @return bool
     */
    protected function hasDeclaringClass($cls)
    {
        return $this->context->hasDeclaringClass($cls);
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

    protected function findAllMethodSameName($method)
    {
        return $this->context->findAllMethodSameName($method);
    }

    protected function existsVertex($type, $key)
    {
        return $this->context->existsVertex($type, $key);
    }

    public function compile()
    {
        // nothing to do
    }

    protected function findMethodInInheritanceTree($cls, $method)
    {
        if ($this->context->hasDeclaringClass($cls)) {
            return $this->context->findMethodInInheritanceTree($cls, $method);
        }
        return null;
    }

}