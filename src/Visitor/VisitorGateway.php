<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;

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

    /**
     * Ctor
     * 
     * @param array $visitor a typelist PHPParser_Node => VisitorState
     */
    public function __construct(array $visitor)
    {
        foreach ($visitor as $k => $v) {
            if (!($v instanceof State\State)) {
                throw new \InvalidArgumentException("Invalid visitor for index $k");
            }
            $v->setContext($this);
            $this->stateList[$v->getName()] = $v;
        }

        $this->stateStack = new \SplObjectStorage();
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
        if (!array_key_exists($stateKey, $this->stateList)) {
            throw new \InvalidArgumentException("$stateKey is not a registered state");
        }
        $this->stateStack[$node] = $this->stateList[$stateKey];
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

}