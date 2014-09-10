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
        $root = $this->recursivAppendTree($level);
        $vSet = $this->graph->getVertexSet();
        $vCard = (2 << $level) - 1;
        $lastLeaf = $this->findVertexByName('>RRRRRR');
        // adding a shortcut
        $this->graph->addEdge($this->findVertexByName('>LL'), $lastLeaf);
        $this->assertCount($vCard, $vSet);

        $path = $this->graph->searchPath($root, $lastLeaf);
        $this->assertCount(3, $path);
        // shortcut found ?
        $this->assertEquals('>LL', $path[2]->getSource()->getName());
        $this->assertEquals('>RRRRRR', $path[2]->getTarget()->getName());
    }

}
