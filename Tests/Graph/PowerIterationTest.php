<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\PowerIteration;
use Trismegiste\Mondrian\Graph\Digraph;

/**
 * PowerIterationTest is a
 */
class PowerIterationTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new PowerIteration(new Digraph());
    }

    protected function tearDown()
    {
        unset($this->graph);
    }

    public function testDiagonal()
    {
        for ($k = 0; $k < 6; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }

        $axis = $this->graph->getVertexSet();
        foreach ($axis as $v) {
            for ($j = 0; $j < 4; $j++) {
                $this->graph->addEdge($v, $axis[rand(0, 5)]);
            }
        }

        $matrix = $this->graph->getAdjacencyMatrix();
        $eigen = $this->graph->getEigenVector(30);
//        print_r($matrix);
//        print_r($eigen);

        $result = array();
        foreach ($matrix as $x => $col) {
            $sum = 0;
            foreach ($col as $y => $coeff) {
                $sum += $coeff * $eigen[$y];
            }
            $result[] = $sum;
        }

        for ($k = 0; $k < 6; $k++) {
//            printf("%f\n", $result[$k] / $eigen[$k]);
        }
    }

}