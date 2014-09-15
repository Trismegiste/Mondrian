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
    protected $stateStack = [];
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

        $this->stateStack[0] = [
            'node' => null,
            'state' => $visitor[0],
            'key' => $visitor[0]->getName()
        ];
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        printf("Entering %s %s %s %d\n", $this->stateStack[0]['key'], $node->getType(), $node->name, count($this->stateStack));
        return $this->stateStack[0]['state']->enter($node);
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        printf("Leaving %s %s %s %d\n", $this->stateStack[0]['key'], $node->getType(), $node->name, count($this->stateStack));
        $ret = $this->stateStack[0]['state']->leave($node);

        if ($this->stateStack[0]['nodeType'] === $node->getType()) {
            array_shift($this->stateStack);
        }

        return $ret;
    }

    public function pushState($stateKey, Node $node)
    {
        printf("Stacking %s %s %s %d\n", $stateKey, $node->getType(), $node->name, count($this->stateStack));
        $state = $this->getState($stateKey);

        array_unshift($this->stateStack, [
            'node' => $node,
            'state' => $state,
            'key' => $state->getName(),
            'nodeType' => $node->getType()
        ]);
    }

    public function getNodeFor($stateKey)
    {
        foreach ($this->stateStack as $assoc) {
            if ($assoc['key'] === $stateKey) {
                return $assoc['node'];
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