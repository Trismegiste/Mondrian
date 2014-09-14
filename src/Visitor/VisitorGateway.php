<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use Trismegiste\Mondrian\Transform\ReflectionContext;
use Trismegiste\Mondrian\Transform\GraphContext;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * VisitorGateway is a multiple patterns for chaining visitors
 * 
 * CoR / State / Visitor
 */
class VisitorGateway extends NodeVisitorAbstract implements State\VisitorContext
{

    /**
     * @var array $stateList Map of state
     */
    protected $stateList = [];

    /**
     * @var array $stateStack Stack of previous state
     */
    protected $stateStack;
    protected $reflectionCtx;
    protected $graphCtx;
    protected $graph;

    /**
     * Ctor
     * 
     * @param array $visitor a list of State
     */
    public function __construct(array $visitor, ReflectionContext $ref, GraphContext $grf, Graph $g)
    {
        $this->graphCtx = $grf;
        $this->graph = $g;
        $this->reflectionCtx = $ref;

        foreach ($visitor as $k => $v) {
            if (!($v instanceof State\State)) {
                throw new \InvalidArgumentException("Invalid visitor for index $k");
            }
            $v->setContext($this);
            $this->stateList[$v->getName()] = $v;
        }

        $this->stateStack = new \SplObjectStorage();
        $this->stateStack->attach(new \stdClass(), $visitor[0]);
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        foreach ($this->stateStack as $keyNode) {
            $v = $this->stateStack->getInfo();
            $ret = $v->enter($node);
            if (!is_null($ret)) {
                return $ret;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        foreach ($this->stateStack as $keyNode) {
            $v = $this->stateStack->getInfo();
            $ret = $v->leave($node);
            if (!is_null($ret)) {
                $this->stateStack->detach($node);
                return $ret;
            }
        }
        $this->stateStack->detach($node);
    }

    public function pushState($stateKey, Node $node)
    {
        $state = $this->getState($stateKey);
        $this->stateStack[$node] = $state;
    }

    public function getNodeFor($stateKey)
    {
        foreach ($this->stateStack as $node) {
            $v = $this->stateStack->getInfo();
            if ($stateKey === $v->getName()) {
                return $node;
            }
        }
    }

    public function getState($stateKey)
    {
        if (!array_key_exists($stateKey, $this->stateList)) {
            throw new \InvalidArgumentException("$stateKey is not a registered state");
        }

        return $this->stateList[$stateKey];
    }

    public function getGraph()
    {
        return $this->graph;
    }

    public function getGraphContext()
    {
        return $this->graphCtx;
    }

    public function getReflectionContext()
    {
        return $this->reflectionCtx;
    }

}