<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;
use Trismegiste\Mondrian\Graph\Algorithm;

/**
 * LiskovSearch is an analyser
 */
class LiskovSearch extends Algorithm implements Generator
{

    /**
     * Generate a digraph reduced to all calls to concrete method
     */
    public function createReducedGraph()
    {
        $reducedGraph = new \Trismegiste\Mondrian\Graph\Digraph();
        $edgeSet = $this->getEdgeSet();
        foreach ($this->getVertexSet() as $cls) {
            if ($cls instanceof ClassVertex) {
                // for each class
                foreach ($this->getEdgeIterator($cls) as $methodVertex) {
                    if ($methodVertex instanceof MethodVertex) {
                        // we have a method first declared in a class
                        // we search for calls to that method
                        foreach ($edgeSet as $call) {
                            if (($call->getSource() instanceof ImplVertex)
                                    && ($call->getTarget() === $methodVertex)) {
                                $impl = $call->getSource();
                                preg_match('#^([^:]+)::#', $impl->getName(), $extract);
                                $owningClass = $extract[1];
                                // we search for the owning class of that impl
                                foreach ($this->getSuccessor($impl) as $succ) {
                                    if (($succ instanceof ClassVertex)
                                            && ($succ->getName() == $owningClass )) {
                                        // we found the owning class vertex of $impl
                                        // add edges to reduced graph
                                        $reducedGraph->addEdge($methodVertex, $cls);
                                        $reducedGraph->addEdge($succ, $methodVertex);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } // arf

        return $reducedGraph;
    }

}
