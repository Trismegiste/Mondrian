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

    public function testRandom()
    {
        $vCard = 20;
        $eCard = $vCard * 4;
        for ($k = 0; $k < $vCard; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }
        $vSet = $this->graph->getVertexSet();
        for ($k = 0; $k < $eCard; $k++) {
            $src = rand(0, $vCard - 1);
            $dst = rand(0, $vCard - 1);
            if ($src != $dst) {
                $this->graph->addEdge($vSet[$src], $vSet[$dst]);
            }
        }

        $this->graph->getDistance();
    }

}