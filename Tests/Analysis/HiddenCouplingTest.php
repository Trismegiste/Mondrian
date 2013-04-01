<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis;

use Trismegiste\Mondrian\Analysis\HiddenCoupling;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex;

/**
 * HiddenCouplingTest tests HiddenCoupling analysis
 *
 */
class HiddenCouplingTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new HiddenCoupling(new Digraph());
    }

    public function testEmptyGraph()
    {
        $result = $this->graph->generateGraph();
        $this->assertCount(0, $result->getVertexSet());
    }

    public function getSourceCode()
    {
        $cc = new Vertex\ClassVertex('A');
        $impl = new Vertex\ImplVertex('A::caller');
        $dc = new Vertex\ClassVertex('B');
        $mth = new Vertex\MethodVertex('B::callee');

        return array(array($cc, $impl, $dc, $mth));
    }

    /**
     * @dataProvider getSourceCode
     */
    public function testHiddenCouplingGraph($callingClass, $impl, $declaring, $callee)
    {
        $this->graph->addEdge($callingClass, $impl);
        $this->graph->addEdge($impl, $callingClass);
        $this->graph->addEdge($declaring, $callee);
        $this->graph->addEdge($impl, $callee);
        $result = $this->graph->generateGraph();
        $this->assertCount(4, $result->getVertexSet());
        $this->assertCount(3, $result->getEdgeSet());
    }

    /**
     * @dataProvider getSourceCode
     */
    public function testNoHiddenCouplingGraph($callingClass, $impl, $declaring, $callee)
    {
        $param = new Vertex\ParamVertex('A::caller/0');
        $this->graph->addEdge($callingClass, $impl);
        $this->graph->addEdge($impl, $callingClass);
        $this->graph->addEdge($declaring, $callee);
        $this->graph->addEdge($impl, $callee);
        $this->graph->addEdge($param, $declaring);
        $this->graph->addEdge($impl, $param);
        $result = $this->graph->generateGraph();
        $this->assertCount(0, $result->getVertexSet());
    }

    /**
     * @dataProvider getSourceCode
     * @expectedException RuntimeException
     */
    public function testBadGraph1($callingClass, $impl, $declaring, $callee)
    {
        $this->graph->addEdge($callingClass, $impl);
        $this->graph->addEdge($declaring, $callee);
        $this->graph->addEdge($impl, $callee);
        $result = $this->graph->generateGraph();
    }

    /**
     * @dataProvider getSourceCode
     * @expectedException RuntimeException
     */
    public function testBadGraph2($callingClass, $impl, $declaring, $callee)
    {
        $this->graph->addEdge($callingClass, $impl);
        $this->graph->addEdge(new Vertex\InterfaceVertex('C'), $callee);
        $this->graph->addEdge($impl, $callingClass);
        $this->graph->addEdge($impl, $callee);
        $result = $this->graph->generateGraph();
    }

}