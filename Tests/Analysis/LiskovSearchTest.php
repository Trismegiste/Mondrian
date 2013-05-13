<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis;

use Trismegiste\Mondrian\Analysis\LiskovSearch;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex;

/**
 * LiskovSearchTest tests LiskovSearch analysis
 *
 */
class LiskovSearchTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new LiskovSearch(new Digraph());
        $this->reduced = new Digraph();
    }

    public function testEmptyGraph()
    {
        $result = $this->graph->createReducedGraph();
        $this->assertCount(0, $result->getVertexSet());
    }

    public function getSourceCode()
    {
        $cc = new Vertex\ClassVertex('A');
        $impl = new Vertex\ImplVertex('A::caller');
        $dc = new Vertex\ClassVertex('B');
        $mth = new Vertex\MethodVertex('B::callee');
        $called = new Vertex\ImplVertex('B::callee');

        return array(array($cc, $impl, $dc, $mth, $called));
    }

    /**
     * @dataProvider getSourceCode
     */
    public function testCouplingGraph($callingClass, $impl, $declaring, $callee, $called)
    {
        $this->graph->addEdge($callingClass, $impl);
        $this->graph->addEdge($impl, $callingClass);
        $this->graph->addEdge($declaring, $callee);
        $this->graph->addEdge($callee, $called);
        $this->graph->addEdge($called, $declaring);

        $result = $this->graph->createReducedGraph();
        $this->assertCount(0, $result->getVertexSet());
        $this->graph->addEdge($impl, $callee);
        $result = $this->graph->createReducedGraph();
        $this->assertCount(3, $result->getVertexSet());
        $this->assertCount(2, $result->getEdgeSet());
    }

}
