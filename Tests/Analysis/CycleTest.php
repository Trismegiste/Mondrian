<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis;

use Trismegiste\Mondrian\Analysis\Cycle;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex;

/**
 * CycleTest tests Cycle analysis
 *
 */
class CycleTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new Cycle(new Digraph());
    }

    public function testEmptyGraph()
    {
        $result = $this->graph->getPartition();
        $this->assertCount(0, $result);
    }

    public function getSourceCode()
    {
        $dc = new Vertex\ClassVertex('B');
        $mth = new Vertex\MethodVertex('B::callee');
        $impl = new Vertex\ImplVertex('B::callee');

        return array(array($dc, $mth, $impl));
    }

    /**
     * @dataProvider getSourceCode
     */
    public function testConcreteCycle($cls, $meth, $impl)
    {
        $this->graph->addEdge($cls, $meth);
        $this->graph->addEdge($meth, $impl);
        $this->graph->addEdge($impl, $cls);
        $result = $this->graph->getPartition();
        $this->assertCount(1, $result);
        $this->assertCount(3, $result[0]);
    }

    /**
     * @dataProvider getSourceCode
     */
    public function testFilter($cls, $meth, $impl)
    {
        $this->graph->addEdge($cls, $impl);
        $this->graph->addEdge($impl, $cls);
        $result = $this->graph->getPartition();
        $this->assertCount(0, $result, "No cycle under 3 components");
    }

}