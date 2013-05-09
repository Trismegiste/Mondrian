<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\ReflectionContext;
use Trismegiste\Mondrian\Transform\GraphContext;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * PassCollector is an abstract compiler pass for visiting source code
 * and build the graph with the help of a Context
 * 
 * It feels like a Mediator between the two Context and concrete PassCollector
 * (It is not one because Context and concrete PassCollector do not share
 * a common interface)
 */
abstract class PassCollector extends PublicCollector
{

    protected $graph;
    private $reflection;
    private $vertexDict;

    public function __construct(ReflectionContext $ref, GraphContext $grf, Graph $g)
    {
        $this->reflection = $ref;
        $this->vertexDict = $grf;
        $this->graph = $g;
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
        return $this->reflection->getDeclaringClass($cls, $meth);
    }

    /**
     * Is FQCN an interface ?
     *
     * @param string $cls FQCN
     * @return bool
     */
    protected function isInterface($cls)
    {
        return $this->reflection->isInterface($cls);
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
        return $this->vertexDict->findVertex($type, $key);
    }

    /**
     * See Context
     */
    protected function findAllMethodSameName($method)
    {
        return $this->vertexDict->findAllMethodSameName($method);
    }

    /**
     * See Context
     */
    protected function existsVertex($type, $key)
    {
        return $this->vertexDict->existsVertex($type, $key);
    }

    /**
     * Check if the class exists before searching for the 
     * declaring class of the method, because class could be unknown, outside
     * or code could be bugged
     */
    protected function findMethodInInheritanceTree($cls, $method)
    {
        if ($this->reflection->hasDeclaringClass($cls)) {
            return $this->reflection->findMethodInInheritanceTree($cls, $method);
        }

        return null;
    }

    /**
     * See Context
     */
    protected function indicesVertex($typ, $index, Vertex $v)
    {
        $this->vertexDict->indicesVertex($typ, $index, $v);
    }

    protected function logFallbackCall($class, $method, $called)
    {
        
    }

    protected function getExcludedCall($class, $method)
    {
        return $this->vertexDict->getExcludedCall($class, $method);
    }

}