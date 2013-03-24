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

    /**
     * Return the dominate eigenvector of the adjacency matrix of
     * this graph
     * 
     * @param float $precision
     * @return \SplObjectStorage 
     */
    public function getEigenVectorSparse($precision=0.001)
    {
        $vertex = $this->getVertexSet();
        $dimension = count($vertex);
        $approx = new \SplObjectStorage();
        foreach ($vertex as $v) {
            $approx[$v] = 1 / sqrt($dimension);
        }
        $iter = 0;
        do {
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
            foreach ($result as $v) {
                $sum += $result->getInfo() * $result->getInfo();
            }
            $norm = sqrt($sum);
            $delta = 0;
            foreach ($result as $v) {
                $newApproxForV = $result->getInfo() / $norm;
                $delta += abs($approx[$v] - $newApproxForV);
                $approx[$v] = $newApproxForV;
            }
            $iter++;
        } while ($delta > $precision);

        return $approx;
    }

}