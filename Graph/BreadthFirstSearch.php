<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * BreadthFirstSearch is ...
 *
 * @author flo
 */
class BreadthFirstSearch extends Algorithm
{

    protected $stack = array();
    protected $excludedEdge = array();

    public function searchPath(Vertex $src, Vertex $dst)
    {
        $this->stack = array();
        $start = new \SplObjectStorage();
        $start[$src] = $src;
        $this->recursivSearchPath($start, $dst);

        return $this->stack;
    }

    protected function recursivSearchPath(\SplObjectStorage $src, Vertex $dst)
    {
        $nextLevel = new \SplObjectStorage();
        foreach ($src as $v) {
            if ($v == $dst) {
                return $v;
            }
            $choice = $this->graph->getEdgeIterator($v);
            foreach ($choice as $succ) {
                $edge = $choice->getInfo();
                if (!isset($edge->visited)) {
                    $nextLevel[$edge->getTarget()] = $v;
                    $edge->visited = true;
                }
            }
        }

        $ret = $this->recursivSearchPath($nextLevel, $dst);
        if (!is_null($ret)){
            array_unshift($this->stack, $nextLevel[$ret]);
        }
    }

}