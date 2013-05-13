<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * Design pattern: Decorator
 * Component : Decorator
 *
 * Algorithm is an algorithm on Graph. This class does nothing except
 * wrapping the graph. It is intended to avoid copy/paste of this content
 * for real algorithms. See it as a default implementation. Therefore
 * you only need to subclass this decorator and add your methods.
 *
 * It is not abstract since I want to test it but the spirit is :)
 *
 * @todo Make it abstract and use mockup to test it by Hell !
 */
class Algorithm implements Graph
{

    protected $graph;

    /**
     * Decorates the graph
     *
     * @param Graph $g
     */
    public function __construct(Graph $g)
    {
        $this->graph = $g;
    }

    public function addEdge(Vertex $source, Vertex $target)
    {
        $this->graph->addEdge($source, $target);
    }

    public function addVertex(Vertex $v)
    {
        $this->graph->addVertex($v);
    }

    public function getEdgeSet()
    {
        return $this->graph->getEdgeSet();
    }

    public function getVertexSet()
    {
        return $this->graph->getVertexSet();
    }

    public function searchEdge(Vertex $source, Vertex $target)
    {
        return $this->graph->searchEdge($source, $target);
    }

    public function getSuccessor(Vertex $v)
    {
        return $this->graph->getSuccessor($v);
    }

    /**
     * {@inheritDoc}
     */
    public function getEdgeIterator(Vertex $v)
    {
        return $this->graph->getEdgeIterator($v);
    }

    /**
     * {@inheritDoc}
     */
    public function getPartition()
    {
        return $this->graph->getPartition();
    }

}
