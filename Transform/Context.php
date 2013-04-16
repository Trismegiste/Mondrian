<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * Context is a context of parser. 
 * Responsible for maintaining a list of methods, classes and interfaces used
 * for building inheritance links in a digraph
 * 
 */
class Context
{

    public $graph;
    public $inheritanceMap; // @todo must go protected
    public $vertex; // @todo must go protected

    /**
     * @todo Perhaps the graph has no place here, it's only easier for initializing CompilerPass
     * @param Graph $g 
     */

    public function __construct(Graph $g)
    {
        $this->graph = $g;
        $this->vertex = array('class' => array(), 'interface' => array(),
            'method' => array(), 'impl' => array(),
            'param' => array()
        );
        $this->inheritanceMap = array();
    }

    /**
     * Construct the inheritanceMap by resolving which class or interface
     * first declares a method
     * 
     * (not vey efficient algo, I admit)
     */
    public function resolveSymbol()
    {
        foreach ($this->inheritanceMap as $className => $info) {
            $method = $info['method'];
            foreach ($method as $methodName => $declaringClass) {
                $upper = $this->recursivDeclaration($declaringClass, $methodName);
                if (!is_null($upper)) {
                    $this->inheritanceMap[$className]['method'][$methodName] = $upper;
                }
            }
        }
    }

    private function recursivDeclaration($current, $m)
    {
        $higher = null;

        if (array_key_exists($m, $this->inheritanceMap[$current]['method'])) {
// default declarer :
            $higher = $this->inheritanceMap[$current]['method'][$m];
        } elseif (interface_exists($current) || class_exists($current)) {
            if (method_exists($current, $m)) {
                $higher = $current;
            }
        }

// higher parent ?
        foreach ($this->inheritanceMap[$current]['parent'] as $mother) {
            $tmp = $this->recursivDeclaration($mother, $m);
            if (!is_null($tmp)) {
                $higher = $tmp;
                break;
            }
        }

        return $higher;
    }

    /**
     * Find if method is declared in superclass.
     * 
     * Note1 : Algo is DFS
     * 
     * Note2: Must be called AFTER resolveSymbol
     * 
     * @param string $cls
     * @param string $method
     * @return string the class which first declare the method (or null)
     */
    public function findMethodInInheritanceTree($cls, $method)
    {
        if (array_key_exists($method, $this->inheritanceMap[$cls]['method'])) {
            return $this->inheritanceMap[$cls]['method'][$method];
        } else {
// higher parent ?
            foreach ($this->inheritanceMap[$cls]['parent'] as $mother) {
                if (!is_null($found = $this->findMethodInInheritanceTree($mother, $method))) {
                    return $found;
                }
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
    public function hasDeclaringClass($cls)
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
    public function getDeclaringClass($cls, $meth)
    {
        return $this->inheritanceMap[$cls]['method'][$meth];
    }

    /**
     * Is FQCN an interface ?
     *
     * @param string $cls FQCN
     * @return bool
     */
    public function isInterface($cls)
    {
        return $this->inheritanceMap[$cls]['interface'];
    }

    /**
     * Find a vertex by its type and name
     *
     * @param string $type
     * @param string $key
     * @return Vertex or null
     */
    public function findVertex($type, $key)
    {
        if (array_key_exists($key, $this->vertex[$type])) {
            return $this->vertex[$type][$key];
        }
        return null;
    }

    public function existsVertex($type, $key)
    {
        return array_key_exists($key, $this->vertex[$type]);
    }

    public function findAllMethodSameName($method)
    {
        return array_filter($this->vertex['method'], function($val) use ($method) {
                            return preg_match("#::$method$#", $val->getName());
                        });
    }

    public function indicesVertex($type, $index, Vertex $v)
    {
        $this->vertex[$type][$index] = $v;
    }

}