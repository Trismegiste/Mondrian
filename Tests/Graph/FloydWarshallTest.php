<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\FloydWarshall;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * FloydWarshallTest is a ...
 *
 * @author florent
 */
class FloydWarshallTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new FloydWarshall(new Digraph());
    }

    public function testEmptyGraph()
    {
        $this->graph->getDistance();
    }

    public function testChain()
    {
        $vCard = 20;
        $eCard = $vCard * 4;
        for ($k = 0; $k < $vCard; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }
        $vSet = $this->graph->getVertexSet();
        for ($k = 0; $k < $vCard; $k++) {
            $this->graph->addEdge($vSet[$k], $vSet[($k + 1) % $vCard]);
        }

        $dist = $this->graph->getDistance();
        $this->assertEquals(0, $dist->get(0, 0));
        $this->assertEquals(19, $dist->get(0, 19));
        $this->assertEquals(19, $dist->get(1, 0));
        $this->assertEquals(19, $dist->get(19, 18));
    }

}