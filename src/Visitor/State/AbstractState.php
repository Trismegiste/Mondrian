<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

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

}