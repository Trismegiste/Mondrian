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

    public function getNodeFor($stateKey);
}