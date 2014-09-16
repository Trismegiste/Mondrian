<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * VisitorContext is a contract for a context of State Pattern
 */
interface VisitorContext
{

    /**
     * Push a new state associated with a node on the stack
     * 
     * @param State $stateKey
     * 
     * @return State the previous state
     */
    public function pushState($stateKey, Node $node);

    /**
     * @param string $stateKey
     * @return Node
     */
    public function getNodeFor($stateKey);

    /**
     * @param string $stateKey
     * @return State
     */
    public function getState($stateKey);

    /**
     * @return \Trismegiste\Mondrian\Transform\ReflectionContext
     */
    public function getReflectionContext();

    /**
     * @return \Trismegiste\Mondrian\Transform\GraphContext
     */
    public function getGraphContext();

    /**
     * @return \Trismegiste\Mondrian\Graph\Graph
     */
    public function getGraph();
}