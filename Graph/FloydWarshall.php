<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

use Trismegiste\Mondrian\Graph\Algorithm;
use Trismegiste\Mondrian\Algebra\ByteMatrix;

/**
 * FloydWarshall is a ...
 *
 * @author florent
 */
class FloydWarshall extends Algorithm
{

    public function getDistance()
    {
        $limit = 32767;
        $vertex = $this->graph->getVertexSet();
        $inverseAssoc = new \SplObjectStorage();
        foreach ($vertex as $idx => $v) {
            $inverseAssoc[$v] = $idx;
        }
        $dimension = count($vertex);
        $dist = new ByteMatrix($dimension);
        for ($line = 0; $line < $dimension; $line++) {
            for ($column = 0; $column < $dimension; $column++) {
                $dist->set($line, $column, ($line === $column) ? 0 : $limit);
            }
        }
        foreach ($this->getEdgeSet() as $edge) {
            $dist->set($inverseAssoc[$edge->getSource()], $inverseAssoc[$edge->getTarget()], 1);
        }

        for ($k = 0; $k < $dimension; $k++) {
            for ($line = 0; $line < $dimension; $line++) {
                for ($column = 0; $column < $dimension; $column++) {
                    $newSum = $dist->get($line, $k) + $dist->get($k, $column);
                    if ($newSum < $dist->get($line, $column)) {
                        $dist->set($line, $column, $newSum);
                    }
                }
            }
        }

        return $dist;
    }

}
