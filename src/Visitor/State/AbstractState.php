<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * AbstractState is a abstract state
 */
abstract class AbstractState implements State
{

    /** @var VisitorContext */
    protected $context;

    public function setContext(VisitorContext $ctx)
    {
        $this->context = $ctx;
    }

    public function leave(Node $node)
    {
        
    }

    /**
     * @return \Trismegiste\Mondrian\Transform\ReflectionContext
     */
    protected function getReflectionContext()
    {
        return $this->context->getReflectionContext();
    }

    /**
     * @return \Trismegiste\Mondrian\Transform\GraphContext
     */
    protected function getGraphContext()
    {
        return $this->context->getGraphContext();
    }

    /**
     * @return \Trismegiste\Mondrian\Graph\Graph
     */
    protected function getGraph()
    {
        return $this->context->getGraph();
    }

}