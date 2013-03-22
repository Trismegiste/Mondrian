<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * PrettyPrint is Decorator for pretty printing a graph
 *
 * @author flo
 */
class PrettyPrint extends Algorithm
{

    private function extractShortName(Vertex $v)
    {
        $result = 'Unknown';
        if (preg_match('#([^\\\\]+)$#', get_class($v), $match)) {
            $result = $match[1];
        }

        return $result;
    }

    public function __toString()
    {
        ob_start();
        foreach ($this->getVertexSet() as $vertex) {
            $cls = $this->extractShortName($vertex);
            echo "$cls : ";
            echo $vertex->getName() . PHP_EOL;
            $edgeList = $this->getSuccessor($vertex);
            foreach ($edgeList as $item) {
                $cls = $this->extractShortName($item);
                echo "  -> $cls : ";
                echo $item->getName() . PHP_EOL;
            }
        }
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

}