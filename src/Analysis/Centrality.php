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
 *
 * What is the point of this tool ?
 *
 * Searching for the degree of a vertex (outer and inner) is not enough
 * because you could have a vertex with many edges but with low impact
 * on source code if this vertex is not in the center of the graph.
 * That's why I use the eigenvector of the digraph like Google does in his
 * pagerank (sort of)
 *
 */
abstract class Centrality extends Algorithm implements VertexDecorator
{

    /**
     * General method to calculate centrality with the power iteration algo
     *
     * @param Graph $g
     * @param string $metaName the key of the metadata name to set in vertices
     */
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

        // filtering only non-zero value
        $threshold = 1;
        foreach ($eigenArray as $idx => $val) {
            if ($val > 1e-5) {
                $threshold++;
            } else {
                break;
            }
        }
        $cardinal = (float) $threshold;

        // only the order is interesting, I'm not Google
        $iter = 0;
        foreach ($eigenArray as $idx => $val) {
            $pseudoRank = min(array($iter / $cardinal, 1));
            $vertexArray[$idx]->setMeta($metaName, $pseudoRank);
            $iter++;
        }
    }

}
