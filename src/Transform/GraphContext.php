<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Transform\Logger\LoggerInterface;

/**
 * GraphContext is a context for building a graph.
 * Indexing the vertices by name
 */
class GraphContext
{

    protected $vertex;
    protected $fineTuning;
    protected $buildLogger;

    /**
     * Build the context
     *
     * @param array $cfg the config
     * @param \Trismegiste\Mondrian\Transform\Logger\LoggerInterface $log a logger
     * @throws \InvalidArgumentException if config is invalid
     */
    public function __construct(array $cfg, LoggerInterface $log)
    {
        if (!array_key_exists('calling', $cfg)) {
            throw new \InvalidArgumentException("No 'calling' key in the config param");
        }
        $this->fineTuning = $cfg;

        $this->vertex = array('class' => array(), 'interface' => array(),
            'method' => array(), 'impl' => array(),
            'param' => array(), 'trait' => []
        );

        $this->buildLogger = $log;
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
     * @param string $type [interface|class|method|param|impl|trait]
     * @param string $index the unique index in this type
     * @param Vertex $v the vertex to index
     */
    public function indicesVertex($type, $index, Vertex $v)
    {
        $this->vertex[$type][$index] = $v;
    }

    /**
     * Get the list of excluded calls for this Class::Method
     *
     * @param string $class
     * @param string $method
     *
     * @return array list of excluded methods "fqcn::methodName"
     */
    public function getExcludedCall($class, $method)
    {
        $ret = array();
        if (array_key_exists("$class::$method", $this->fineTuning['calling'])) {
            $ret = $this->fineTuning['calling']["$class::$method"]['ignore'];
        }

        return $ret;
    }

    /**
     * Log a call found by the fallback
     * 
     * @param string $class
     * @param string $method
     * @param string $called
     */
    public function logFallbackCall($class, $method, $called)
    {
        $this->buildLogger->logCallTo("$class::$method", $called);
    }

}
