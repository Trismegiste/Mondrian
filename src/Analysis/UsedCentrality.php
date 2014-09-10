<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\ReversedDigraph;

/**
 * This tool helps you to find an effect on some components:
 *
 * * The ripple effect
 * One component (class, parameter, method...) can be highly used accross
 * the source code. Each time there is a change in this component, chances
 * are you need to change many other components directly depending on it
 * and so on. That's the ripple. With the "usedRank" method you can view
 * what component is time consuming and can lead to many merge conflicts in
 * Git.
 *
 */
class UsedCentrality extends Centrality
{

    /**
     * Add used centrality information on each vertex
     * (edge effect on this digraph)
     */
    public function decorate()
    {
        $reversed = new ReversedDigraph($this->graph);
        $this->addCentralityRank($reversed->getReversed(), 'centrality');
    }

}
