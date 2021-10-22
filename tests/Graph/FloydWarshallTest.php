<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use PHPUnit\Framework\TestCase;
use Trismegiste\Mondrian\Algebra\Matrix;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\FloydWarshall;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * FloydWarshallTest is a ...
 *
 * @author florent
 */
class FloydWarshallTest extends TestCase
{

    protected $graph;

    protected function setUp(): void
    {
        $this->graph = new FloydWarshall(new Digraph());
    }

    public function testEmptyGraph()
    {
        $this->assertInstanceOf(Matrix::class, $this->graph->getDistance());
    }

    public function testChain()
    {
        $vCard = 20;
        for ($k = 0; $k < $vCard; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }
        $vSet = $this->graph->getVertexSet();
        for ($k = 0; $k < $vCard; $k++) {
            $this->graph->addEdge($vSet[$k], $vSet[($k + 1) % $vCard]);
        }

        $dist = $this->graph->getDistance();
        $this->assertEquals(0, $dist->get(0, 0));
        $this->assertEquals($vCard - 1, $dist->get(0, $vCard - 1));
        $this->assertEquals($vCard - 1, $dist->get(1, 0));
        $this->assertEquals($vCard - 1, $dist->get($vCard - 1, $vCard - 2));
    }

}
