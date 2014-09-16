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

    /**
     * @inheritdoc
     */
    public function setContext(VisitorContext $ctx)
    {
        $this->context = $ctx;
    }

    /**
     * @inheritdoc
     */
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

    /**
     * Search for a vertex of a given type
     * 
     * @param string $type trait|class|interface|param|method|impl
     * @param string $key the key for this vertex
     * 
     * @return \Trismegiste\Mondrian\Graph\Vertex
     */
    protected function findVertex($type, $key)
    {
        return $this->context->getGraphContext()->findVertex($type, $key);
    }

}