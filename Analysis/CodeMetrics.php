<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\Algorithm;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * CodeMetrics analyses a graph
 * Design Pattern : Decorator
 *
 * @author flo
 */
class CodeMetrics extends Algorithm
{

    private function extractShortName(Vertex $v)
    {
        $result = 'Unknown';
        if (preg_match('#([^\\\\]+)Vertex$#', get_class($v), $match)) {
            $result = $match[1];
        }

        return $result;
    }

    public function getCardinal()
    {
        $card = array(
            'Class' => 0,
            'Interface' => 0,
            'Impl' => 0,
            'Method' => 0,
            'Param' => 0,
            'MethodDeclaration' => array('Class' => 0, 'Interface' => 0)
        );
        $vertex = $this->graph->getVertexSet();
        foreach ($vertex as $v) {
            $type = $this->extractShortName($v);
            $card[$type]++;
            if (($type == 'Class') || ($type == 'Interface')) {
                foreach ($this->graph->getSuccessor($v) as $succ) {
                    $succType = $this->extractShortName($succ);
                    if ($succType == 'Method') {
                        $card['MethodDeclaration'][$type]++;
                    }
                }
            }
        }

        return $card;
    }

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

}