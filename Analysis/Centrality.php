<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\Algorithm;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * Centrality analyses a graph and add centrality rank to vertices
 * Design Pattern : Decorator of Graph
 */
class Centrality extends Algorithm
{

    public function getMostDepending()
    {
        $power = new \Trismegiste\Mondrian\Graph\PowerIteration($this->graph);
        $eigen = $power->getEigenVectorSparse();

        $eigenVector = $eigen['vector'];
        $eigenArray = array();
        $vertexArray = array();
        foreach ($eigenVector as $idx => $v) {
            $eigenArray[$idx] = $eigenVector->getInfo();
            $vertexArray[$idx] = $v;
        }
        arsort($eigenArray);
        array_splice($eigenArray, 10);

        $mostDependencies = array();
        foreach ($eigenArray as $idx => $val) {
            $mostDependencies[] = $vertexArray[$idx];
        }

        return $mostDependencies;
    }

    public function getMostDepended()
    {
        $reversed = new \Trismegiste\Mondrian\Graph\ReversedDigraph($this->graph);
        $power = new \Trismegiste\Mondrian\Graph\PowerIteration($reversed->getReversed());

        $eigen = $power->getEigenVectorSparse();

        $eigenVector = $eigen['vector'];
        $eigenArray = array();
        $vertexArray = array();
        foreach ($eigenVector as $idx => $v) {
            $eigenArray[$idx] = $eigenVector->getInfo();
            $vertexArray[$idx] = $v;
        }
        arsort($eigenArray);
        array_splice($eigenArray, 10);

        $mostDependencies = array();
        foreach ($eigenArray as $idx => $val) {
            $mostDependencies[] = $vertexArray[$idx];
        }

        return $mostDependencies;
    }

    protected function addCentralityRank(Graph $g, $metaName)
    {
        $power = new \Trismegiste\Mondrian\Graph\PowerIteration($g);
        $eigen = $power->getEigenVectorSparse();

        $eigenVector = $eigen['vector'];
        $eigenArray = array();
        $vertexArray = array();
        foreach ($eigenVector as $idx => $v) {
            $eigenArray[$idx] = $eigenVector->getInfo();
            $vertexArray[$idx] = $v;
        }
        arsort($eigenArray);
        $upperBound = $eigenArray[0];

        foreach ($eigenArray as $idx => $val) {
            // like google page rank :
            $vertexArray[$idx]->setMeta($metaName, $val * 10 / $upperBound);
        }
    }

}