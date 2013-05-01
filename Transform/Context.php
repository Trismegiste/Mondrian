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

    protected $inheritanceMap;
    protected $vertex;

    /**
     * Build the context
     * 
     * @param Graph $g 
     */
    public function __construct()
    {
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
     * (not vey efficient algo, I admit), it sux, it's redundent, I don't like it
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
     * Note1: Algo is DFS
     * Note2: Must be called AFTER resolveSymbol
     * Note3: this one is kewl, I don't know why it works at the first try
     * 
     * @param string $cls
     * @param string $method
     * 
     * @return string the class which first declares the method (or null)
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
     * Initialize a new symbol
     * 
     * @param string $name class or interface name
     * @param bool $isInterface is interface ?
     */
    public function initSymbol($name, $isInterface)
    {
        if (!array_key_exists($name, $this->inheritanceMap)) {
            $this->inheritanceMap[$name]['interface'] = $isInterface;
            $this->inheritanceMap[$name]['parent'] = array();
            $this->inheritanceMap[$name]['method'] = array();
        }
    }

    /**
     * Stacks a parent type for a type
     * 
     * @param string $cls the type
     * @param string $parent the parent type of $cls
     */
    public function pushParentClass($cls, $parent)
    {
        $this->inheritanceMap[$cls]['parent'][] = $parent;
    }

    /**
     * Add a method to its type with the current type 
     * for its default declaring type (after resolveSymbol, it changes)
     * 
     * @param string $cls
     * @param string $method 
     */
    public function addMethodToClass($cls, $method)
    {
        $this->inheritanceMap[$cls]['method'][$method] = $cls;
    }

    /**
     * Search if a type (class or interface) exists in the inheritanceMap
     *
     * @param string $cls
     * 
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
     * 
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
     * 
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
     * 
     * @return Vertex or null
     */
    public function findVertex($type, $key)
    {
        if (array_key_exists($key, $this->vertex[$type])) {
            return $this->vertex[$type][$key];
        }
        return null;
    }

    /**
     * Returns if a vertex of the type $type with the index $key exists
     * 
     * @param string $type
     * @param string $key
     * 
     * @return bool 
     */
    public function existsVertex($type, $key)
    {
        return array_key_exists($key, $this->vertex[$type]);
    }

    /**
     * Find all methods with the same name whatever its class
     * 
     * @param string $method
     * 
     * @return Vertex[] 
     */
    public function findAllMethodSameName($method)
    {
        return array_filter($this->vertex['method'], function($val) use ($method) {
                            return preg_match("#::$method$#", $val->getName());
                        });
    }

    /**
     * Maintains a hashmap : ( type , index ) => Vertex obj
     * 
     * @param string $type [interface|class|method|param|impl]
     * @param string $index the unique index in this type
     * @param Vertex $v the vertex to index
     */
    public function indicesVertex($type, $index, Vertex $v)
    {
        $this->vertex[$type][$index] = $v;
    }

}