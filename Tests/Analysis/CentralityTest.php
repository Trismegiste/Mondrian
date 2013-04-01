<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis;

use Trismegiste\Mondrian\Analysis\Centrality;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex;

/**
 * CentralityTest tests Centrality analysis
 *
 */
class CentralityTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new Centrality(new Digraph());
    }

    public function testEmptyGraph()
    {
        $this->graph->addDependRank();
        $this->assertCount(0, $this->graph->getVertexSet());
    }

    public function testMostUsed()
    {
        $center = new Vertex\InterfaceVertex('A');
        $meth = new Vertex\MethodVertex('A::some');
        $this->graph->addEdge($center, $meth);

        for ($k = 0; $k < 20; $k++) {
            $subclass = new Vertex\ClassVertex('B' . $k);
            $impl = new Vertex\ImplVertex($k);
            $this->graph->addEdge($subclass, $center);
            $this->graph->addEdge($subclass, $impl);
            $this->graph->addEdge($impl, $subclass);
        }

        $this->graph->addUsedRank();
        $this->assertEquals(1, $meth->getMeta('centrality'));
        $this->graph->addDependRank();
        $this->assertEquals(11, $meth->getMeta('centrality'));
    }

}