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

    protected function getReflectionContext()
    {
        return $this->context->getReflectionContext();
    }

}