<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Visitor;
use Trismegiste\Mondrian\Builder\Compiler\AbstractTraverser;
use Trismegiste\Mondrian\Transform\Logger\LoggerInterface;

/**
 * Design Pattern: Builder
 *
 * GraphBuilder is a builder for a compiler
 *
 */
class GraphBuilder extends AbstractTraverser
{

    protected $graphResult;
    protected $config;
    protected $reflection;
    protected $vertexContext;

    public function __construct(array $cfg, Graph $g, LoggerInterface $log)
    {
        $this->config = $cfg;
        $this->graphResult = $g;
    }

    public function buildContext()
    {
        $this->reflection = new ReflectionContext();
        $this->vertexContext = new GraphContext($this->config);
    }

    public function buildCollectors()
    {
        return array(
            new Visitor\SymbolMap($this->reflection),
            new Visitor\VertexCollector($this->reflection, $this->vertexContext, $this->graphResult),
            new Visitor\EdgeCollector($this->reflection, $this->vertexContext, $this->graphResult)
        );
    }

}