<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\Algorithm;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Graph\PowerIteration;
use Trismegiste\Mondrian\Graph\ReversedDigraph;

/**
 * Centrality analyses a graph and add centrality rank to vertices
 * Design Pattern : Decorator of Graph
 */
class Centrality extends Algorithm
{

    public function addDependRank()
    {
        $this->addCentralityRank($this->graph, 'centrality');
    }

    public function addUsedRank()
    {
        $reversed = new ReversedDigraph($this->graph);
        $this->addCentralityRank($reversed, 'centrality');
    }

    protected function addCentralityRank(Graph $g, $metaName)
    {
        $power = new PowerIteration($g);
        $eigen = $power->getEigenVectorSparse();

        $eigenVector = $eigen['vector'];
        $eigenArray = array();
        $vertexArray = array();
        foreach ($eigenVector as $idx => $v) {
            $eigenArray[$idx] = $eigenVector->getInfo();
            $vertexArray[$idx] = $v;
        }
        arsort($eigenArray);
        // only the order is interesting
        $iter = 0;
        $mostCentral = count($eigenVector) / 10.0;
        foreach ($eigenArray as $idx => $val) {
            $pseudoRank = ($iter < $mostCentral) ? 1 + (10.0 * $iter) / $mostCentral : 11;
            $vertexArray[$idx]->setMeta($metaName, $pseudoRank);
            $iter++;
        }
    }

}