<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * State is a contract for a state of a visitor to support one type of node
 */
interface State
{

    /**
     * Enters into the node
     * 
     * @param \PhpParser\Node $node
     * 
     * @return see NodeVisitorAbstract
     */
    public function enter(Node $node);

    /**
     * leaves the node
     * 
     * @param \PhpParser\Node $node
     *
     * @return see NodeVisitorAbstract
     */
    public function leave(Node $node);

    /**
     * Sets the common context for the State Pattern
     * 
     * @param \Trismegiste\Mondrian\Visitor\State\VisitorContext $ctx
     */
    public function setContext(VisitorContext $ctx);

    public function getName();
}