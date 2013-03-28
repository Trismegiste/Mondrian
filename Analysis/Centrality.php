<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\Algorithm;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * Centrality analyses a graph and add centrality rank to vertices
 * Design Pattern : Decorator of Graph
 */
class Centrality extends Algorithm
{

    public function addDependRank()
    {
        $this->addCentralityRank($this->graph, 'depend');
    }

    public function addUsedRank()
    {
        $reversed = new \Trismegiste\Mondrian\Graph\ReversedDigraph($this->graph);
        $this->addCentralityRank($reversed, 'used');
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