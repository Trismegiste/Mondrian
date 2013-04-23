<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis;

use Trismegiste\Mondrian\Analysis\CouplingMaker;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex;

/**
 * CouplingMakerTest tests CouplingMaker analysis
 *
 */
class CouplingMakerTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new CouplingMaker(new Digraph());
    }

    public function testEmptyGraph()
    {
        $result = $this->graph->createReducedGraph();
        $this->assertCount(0, $result->getVertexSet());
    }

    public function getSourceCode()
    {
        $inter = new Vertex\InterfaceVertex('A');
        $mth = new Vertex\MethodVertex('A::meth');
        $param = new Vertex\ParamVertex('A::meth/0');
        $cls = new Vertex\ClassVertex('B');
        $impl = new Vertex\ImplVertex('B::meth');

        return array(array($inter, $mth, $cls, $impl, $param));
    }

    /**
     * @dataProvider getSourceCode
     */
    public function testCouplingGenerator($intefac, $signature, $concrete, $impl, $param)
    {
        $this->graph->addEdge($intefac, $signature);
        $this->graph->addEdge($signature, $param);
        $this->graph->addEdge($concrete, $intefac);
        $this->graph->addEdge($impl, $concrete);
        $this->graph->addEdge($concrete, $impl);
        $this->graph->addEdge($impl, $param);

        $result = $this->graph->createReducedGraph();
        $this->assertCount(0, $result->getVertexSet());

        $this->graph->addEdge($param, $concrete);
        $result = $this->graph->createReducedGraph();
        $this->assertCount(3, $result->getVertexSet());
        $this->assertCount(2, $result->getEdgeSet());
    }

}