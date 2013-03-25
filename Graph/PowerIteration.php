<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * PowerIteration is a the a decorator for Power Iteration algorithm
 *
 * In mathematics, the power iteration is an eigenvalue algorithm: given a
 * matrix A, the algorithm will produce a number λ (the eigenvalue) and a
 * nonzero vector v (the eigenvector), such that Av = λv. The algorithm is
 * also known as the Von Mises iteration.[1]
 *
 * http://en.wikipedia.org/wiki/Power_iteration
 *
 * In general, there will be many different eigenvalues  for which an
 * eigenvector solution exists. However, the additional requirement that all
 * the entries in the eigenvector be positive implies (by the Perron–Frobenius
 * theorem) that only the greatest eigenvalue results in the desired centrality
 * measure.[12] The  component of the related eigenvector then gives the
 * centrality score of the vertex  in the network. Power iteration is one of
 * many eigenvalue algorithms that may be used to find this
 * dominant eigenvector.
 *
 * http://en.wikipedia.org/wiki/Eigenvector_centrality#Eigenvector_centrality
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
    public function getEigenVectorSparse($precision = 0.001)
    {
        $vertex = $this->getVertexSet();
        $dimension = count($vertex);
        $approx = new \SplObjectStorage();
        foreach ($vertex as $v) {
            $approx[$v] = 1 / sqrt($dimension);
        }
        $iter = 0;
        $norm = 1;
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
            $newNorm = sqrt($sum);
            $delta = abs($norm - $newNorm);
            $norm = $newNorm;

            foreach ($result as $v) {
                $approx[$v] = $result->getInfo() / $norm;
            }
            $iter++;
        } while ($delta > $precision);

        return array('value' => $norm, 'vector' => $approx);
    }

}