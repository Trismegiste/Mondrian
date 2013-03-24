<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * Design pattern: Decorator
 * Component : Concrete Component
 *
 * Graph is a directed graph with 0 or 1 directed edge between
 * two vertices. Therefore, there are at maximum two edges
 * between two vertices (the two directions)
 */
class Digraph implements Graph
{

    protected $adjacency;

    public function __construct()
    {
        $this->adjacency = new \SplObjectStorage();
    }

    /**
     * Add a vertex to the graph without edge
     *
     * @param Vertex $v
     */
    public function addVertex(Vertex $v)
    {
        // if the vetex is already in the the adjacencies list, there is no
        // need to add it.
        if (!$this->adjacency->contains($v)) {
            // if it is not found, we add it (with empty edge list)
            $this->adjacency[$v] = new \SplObjectStorage();
        }
    }

    /**
     * Add a directed edge if it does not already exist
     *
     * @param Vertex $source
     * @param Vertex $target
     */
    public function addEdge(Vertex $source, Vertex $target)
    {
        $this->addVertex($source);
        // if there is not already a directed edge between those two vertices
        // we drop the stacking
        if (is_null($this->searchEdge($source, $target))) {
            $this->addVertex($target);
            $this->adjacency[$source][$target] = new Edge($source, $target);
        }
    }

    /**
     * Searches an existing directed edge between two vertices
     *
     * @param Vertex $source
     * @param Vertex $target
     * @return Edge
     */
    public function searchEdge(Vertex $source, Vertex $target)
    {
        if ($this->adjacency->contains($source)) {
            if ($this->adjacency[$source]->contains($target)) {
                return $this->adjacency[$source][$target];
            }
        }

        return null;
    }

    /**
     * Get the vertices in the graph
     *
     * @return array
     */
    public function getVertexSet()
    {
        $set = array();
        foreach ($this->adjacency as $vertex) {
            $set[] = $vertex;
        }

        return $set;
    }

    /**
     * Get the edges set
     *
     * @return array
     */
    public function getEdgeSet()
    {
        $set = array();
        foreach ($this->adjacency as $vertex) {
            $edgeList = $this->adjacency->getInfo();
            foreach ($edgeList as $item) {
                $set[] = $edgeList->getInfo();
            }
        }

        return $set;
    }

    /**
     * Returns successor(s) of a given vertex (a.k.a all vertices targeted
     * by edges coming from the given vertex)
     * 
     * @param Vertex $v
     * @return array array of Vertex
     */
    public function getSuccessor(Vertex $v)
    {
        $set = null;
        if ($this->adjacency->contains($v)) {
            $set = array();
            foreach ($this->adjacency[$v] as $succ) {
                $set[] = $succ;
            }
        }

        return $set;
    }

}