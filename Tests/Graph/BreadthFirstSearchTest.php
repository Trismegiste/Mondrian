<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\BreadthFirstSearch;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Edge;

/**
 * BreadthFirstSearchTest is a test for BFS algo
 *
 * @author flo
 */
class BreadthFirstSearchTest extends SearchPathTest
{

    protected function createGraph(Digraph $g)
    {
        return new BreadthFirstSearch($g);
    }

    public function testBinaryWithShortcut()
    {
        $level = 6;
        $root = new Vertex('>');
        $this->recursivCreateTree($level, $root);
        $vSet = $this->graph->getVertexSet();
        $vCard = (2 << $level) - 1;
        $lastLeaf = $vSet[$vCard - 1];
        // adding a shortcut
        $this->graph->addEdge($vSet[3], $lastLeaf);
        $this->assertCount($vCard, $vSet);

        $path = $this->graph->searchPath($root, $lastLeaf);
        $this->assertCount(3, $path);
        // shortcut found ?
        $this->assertEquals('>LL', $path[2]->getSource()->getName());
        $this->assertEquals('>RRRRRR', $path[2]->getTarget()->getName());
    }

}