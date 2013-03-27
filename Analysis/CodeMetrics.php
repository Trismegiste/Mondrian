<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\Algorithm;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * CodeMetrics analyses a graph and counts number of vertices per type
 * Design Pattern : Decorator
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

}