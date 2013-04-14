<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis;

use Trismegiste\Mondrian\Analysis\SpaghettiCoupling;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex;
use Trismegiste\Mondrian\Analysis\Strategy\ByCalling;

/**
 * SpaghettiCouplingTest tests SpaghettiCoupling analysis
 *
 */
class SpaghettiCouplingTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;
    protected $reduced;

    protected function setUp()
    {
        $this->graph = new SpaghettiCoupling(new Digraph());
        $this->reduced = new Digraph();
        $this->graph->setFilterPath(new ByCalling($this->reduced));
    }

    public function testEmptyGraph()
    {
        $this->graph->generateCoupledClassGraph();
        $this->assertCount(0, $this->reduced->getVertexSet());
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
        $this->graph->generateCoupledClassGraph();
        $result = & $this->reduced;
        $this->assertCount(0, $result->getVertexSet());
        $this->graph->addEdge($impl, $callee);
        $this->graph->generateCoupledClassGraph();
        $this->assertCount(2, $result->getVertexSet());
        $this->assertCount(1, $result->getEdgeSet());
    }

}