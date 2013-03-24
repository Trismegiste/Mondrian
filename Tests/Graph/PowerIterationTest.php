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

        $dim = 9;
        for ($k = 0; $k < $dim; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }

        $axis = $this->graph->getVertexSet();
        foreach ($axis as $v) {
            for ($j = 0; $j < (2 * $dim / 3); $j++) {
                $this->graph->addEdge($v, $axis[rand(0, $dim - 1)]);
            }
        }
    }

    protected function tearDown()
    {
        unset($this->graph);
    }

    public function testEigen()
    {
        $stopWatch = time();
        $mem = memory_get_usage();
        $eigen = $this->graph->getEigenVector(30);
        printf("%d sec\n%d bytes\n", time() - $stopWatch, memory_get_usage() - $mem);

        $result = array();
        $matrix = $this->graph->getAdjacencyMatrix();
        foreach ($matrix as $x => $col) {
            $sum = 0;
            foreach ($col as $y => $coeff) {
                $sum += $coeff * $eigen[$y];
            }
            $result[] = $sum;
        }

        foreach ($eigen as $k => $val) {
            //     printf("%f\n", $result[$k] / $val);
        }
    }

    public function testEigenSparse()
    {
        $stopWatch = time();
        $mem = memory_get_usage();
        $eigen = $this->graph->getEigenVectorSparse(30);
        printf("%d sec\n%d bytes\n", time() - $stopWatch, memory_get_usage() - $mem);

        $result = new \SplObjectStorage();
        foreach ($eigen as $vx) {
            $sum = 0;
            foreach ($this->graph->getSuccessor($vx) as $vy) {
                $sum += $eigen[$vy];
            }
            $result[$vx] = $sum;
        }

        foreach ($eigen as $vx) {
            //   printf("%f\n", $result[$vx] / $eigen[$vx]);
        }
    }

}