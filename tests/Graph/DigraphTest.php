<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * DigraphTest is a test for Digraph
 */
class DigraphTest extends GraphTest
{

    protected function createGraphInstance()
    {
        return new Digraph();
    }

    public function testNoLoop()
    {
        $this->expectException(\InvalidArgumentException::class);
        $v = new Vertex('f');
        $this->graph->addEdge($v, $v);
    }

}
