<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * GraphContext is a context for building a graph. 
 * Indexing the vertices by name 
 */
class GraphContext
{

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