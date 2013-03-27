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
        $max = 0;
        $found = null;
        foreach ($eigenVector as $v => $val) {
            if ($val > $max) {
                $max = $val;
                $found = $v;
            }
        }

        return $found;
    }

}