<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * PowerIteration is a the a decorator for Power Iteration algorithm
 *
 * http://en.wikipedia.org/wiki/Power_iteration
 *
 */
class PowerIteration extends Algorithm
{

    public function getAdjacencyMatrix()
    {
        $matrix = array();
        $axis = $this->graph->getVertexSet();

        foreach ($axis as $x => $vx) {
            foreach ($axis as $y => $vy) {
                $matrix[$x][$y] = is_null($this->graph->searchEdge($vx, $vy)) ? 0 : 1;
            }
        }

        return $matrix;
    }

    public function getEigenVector($iter)
    {
        $matrix = $this->getAdjacencyMatrix();
        $dimension = count($matrix);
        $approx = array_fill(0, $dimension, 1);
        for ($k = 0; $k < $iter; $k++) {
            // result = M . approx
            $result = array_fill(0, $dimension, 0);
            for ($x = 0; $x < $dimension; $x++) {
                for ($y = 0; $y < $dimension; $y++) {
                    $result[$x] += $matrix[$x][$y] * $approx[$y];
                }
            }
            // normalize
            $sum = 0;
            foreach ($result as $val) {
                $sum += $val * $val;
            }
            $approx = array_map(
                    function($x) use ($sum) {
                        return $x / sqrt($sum);
                    }, $result);
        }

        return $approx;
    }

    public function getEigenVectorSparse($iter)
    {
        $vertex = $this->getVertexSet();
        $approx = new \SplObjectStorage();
        foreach ($vertex as $v) {
            $approx[$v] = 1;
        }
        for ($k = 0; $k < $iter; $k++) {
            // result = M . approx
            $result = new \SplObjectStorage();
            foreach ($vertex as $v) {
                $result[$v] = 0;
            }
            foreach ($vertex as $v) {
                foreach ($this->graph->getSuccessor($v) as $succ) {
                    $result[$v] += $approx[$succ];
                }
            }
            // normalize
            $sum = 0;
            foreach ($result as $val) {
                $sum += $result->getInfo() * $result->getInfo();
            }
            $norm = sqrt($sum);
            foreach ($result as $v) {
                $approx[$v] = $result[$v] / $norm;
            }
        }

        return $approx;
    }

}