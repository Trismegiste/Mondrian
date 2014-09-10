<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * Design pattern: Decorator
 * Component : Concrete Component
 *
 * Digraph is a directed graph with 0 or 1 directed edge between
 * two vertices. Therefore, there are at maximum two edges
 * between two vertices (the two directions)
 */
class Digraph implements Graph
{

    /**
     * This is a hashmap Source Vertex -> \SplObjectStorage (the adjacencies list
     * of one vertex)
     *
     * The adjacencies list of one vertex is a hashmap Target vertex -> Edge
     *
     * @var \SplObjectStorage
     */
    protected $adjacency;

    public function __construct()
    {
        $this->adjacency = new \SplObjectStorage();
    }

    /**
     * Add a vertex to the graph without edge
     * Note : Idempotent method
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
     *
     * @throws \InvalidArgumentException if source and target are the same
     */
    public function addEdge(Vertex $source, Vertex $target)
    {
        if ($source === $target) {
            throw new \InvalidArgumentException('No loop in digraph');
        }
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
     * @return Edge the edge or null
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
     * @return Vertex[]
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
     * @return Edge[]
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
     * @return Vertex[] array of successor vertex
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

    /**
     * {@inheritDoc}
     */
    public function getEdgeIterator(Vertex $v)
    {
        return $this->adjacency[$v];
    }

    /**
     * {@inheritDoc}
     */
    public function getPartition()
    {
        return array();
    }

}
