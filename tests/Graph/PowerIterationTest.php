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

        $dim = 20;
        for ($k = 0; $k < $dim; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }

        $axis = $this->graph->getVertexSet();
        foreach ($axis as $v) {
            foreach ($axis as $w) {
                if (($v != $w) && (rand(0, 9) >= 7)) {
                    $this->graph->addEdge($v, $w);
                }
            }
        }
    }

    protected function tearDown()
    {
        unset($this->graph);
    }

    public function loop()
    {
        $ret = array();
        for ($k = 0; $k < 10; $k++) {
            $ret[] = array($k);
        }
        return $ret;
    }

    /**
     * @dataProvider loop
     * @param type $iter
     */
    public function testEigenSparse($iter)
    {
        $eigen = $this->graph->getEigenVectorSparse();
        $eigenVector = $eigen['vector'];

        $eigenValue = 0;
        $nbComp = 0;
        foreach ($eigenVector as $vx) {
            $sum = 0;
            foreach ($this->graph->getSuccessor($vx) as $vy) {
                $sum += $eigenVector[$vy];
            }
            if ($eigenVector[$vx] != 0) {
                $eigenValue += $sum / $eigenVector[$vx];
                $nbComp++;
            }
        }
        $eigenValue /= $nbComp;

        $this->assertLessThan(0.001, abs($eigen['value'] - $eigenValue));
    }

}
