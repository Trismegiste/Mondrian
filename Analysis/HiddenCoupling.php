<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\BreadthFirstSearch;
use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;
use Trismegiste\Mondrian\Transform\Vertex\InterfaceVertex;
use Trismegiste\Mondrian\Graph\Edge;

/**
 * HiddenCoupling is an analyser which checks and finds hidden coupling
 * between types
 * 
 * How ? 
 * This analyser searches for method calls. Everytime there is a call of
 * a method against an object ( $obj->getThing() ), it means an edge from
 * an implementation vertex where the call is to a method signature vertex.
 * 
 * Since "$obj" does not come from nowhere, its type (class or interface)
 * must be known by the class owning the implementation vertex. 
 * In other words : 
 * If there is an edge from an implementation to a method, there must be
 * at least one another directed path between these two vertices 
 * (through the class vertex, through a parameter vertex, superclass etc...)
 * If you can't figure why, I recommand you to read the digraph language
 * I've defined in this intent.
 * 
 * If there is none, *maybe* it means a hidden coupling. I add the "maybe"
 * because, it's hard to find the type of "$obj" in soft-typed language like
 * PHP. That's why there can be false positive. But it's easier to check
 * false positives than to search through all over the php files to find
 * that kind of weakness in the code.
 * 
 * One another thing, since I cannot detect calls from "call_user_func" and
 * other magic features of PHP like "$obj->$methodName()" or "new $className"
 * there is a big limit of this static analyser.
 * 
 * Neverthesless I pretend this tool can find at least 50% of hidden coupling
 * in poorly-coded classes and about 10% of false positive, from what I've
 * seen.
 * 
 */
class HiddenCoupling extends BreadthFirstSearch
{

    /**
     * Generate a digraph reduced to the hidden coupled vertices
     */
    public function generateGraph()
    {
        $reducedGraph = new \Trismegiste\Mondrian\Graph\Digraph();
        $dependency = $this->getEdgeSet();
        foreach ($dependency as $edge) {
            if (($edge->getSource() instanceof ImplVertex)
                    && ($edge->getTarget() instanceof MethodVertex)) {

                $this->resetVisited();
                $edge->visited = true;
                $otherPath = $this->searchPath($edge->getSource(), $edge->getTarget());

                if (count($otherPath) == 0) {
                    // not found => hidden coupling :
                    // source is impl and target is method
                    $reducedGraph->addEdge($edge->getSource(), $edge->getTarget());
                    $reducedGraph->addEdge(
                            $this->findOwningClassVertex($edge->getSource()), $edge->getSource());
                    $reducedGraph->addEdge(
                            $this->findDeclaringVertex($edge->getTarget()), $edge->getTarget());
                }
            }
        }

        return $reducedGraph;
    }

    protected function findOwningClassVertex(ImplVertex $impl)
    {
        list($className, $methodName) = explode('::', $impl->getName());
        foreach ($this->graph->getSuccessor($impl) as $succ) {
            if (($succ instanceof ClassVertex)
                    && ($succ->getName() == $className)) {

                return $succ;
            }
        }

        throw new \RuntimeException("$methodName has no owning class");
    }

    protected function findDeclaringVertex(MethodVertex $meth)
    {
        list($className, $methodName) = explode('::', $meth->getName());
        foreach ($this->graph->getEdgeSet() as $edge) {
            if ($edge->getTarget() == $meth) {
                $src = $edge->getSource();
                if (($src instanceof ClassVertex) || ($src instanceof InterfaceVertex)) {
                    if ($src->getName() == $className) {
                        return $src;
                    }
                }
            }
        }

        throw new \RuntimeException("$methodName has no declaring class");
    }

}