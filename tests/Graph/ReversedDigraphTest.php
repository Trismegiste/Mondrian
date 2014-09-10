<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\ReversedDigraph;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * ReversedDigraphTest is a test class for ReversedDigraph
 */
class ReversedDigraphTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new ReversedDigraph(new Digraph());
    }

    public function testReversed()
    {
        $card = 10;
        for ($k = 0; $k < $card; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }

        $vertex = $this->graph->getVertexSet();
        foreach ($vertex as $idx => $v) {
            // the first vertex is isolated
            for ($k = 0; $k < $idx; $k++) {
                $this->graph->addEdge($v, $vertex[$k]);
            }
        }

        $reversed = $this->graph->getReversed();
        $this->assertCount($card, $reversed->getVertexSet());
        $this->assertCount($card * ($card - 1) / 2, $reversed->getEdgeSet());

        $newGraph = new ReversedDigraph($reversed);
        $origin = $newGraph->getReversed();
        $this->assertEquals($this->graph->getVertexSet(), $origin->getVertexSet());
        $this->assertEquals($this->graph->getEdgeSet(), $origin->getEdgeSet());
    }

}
