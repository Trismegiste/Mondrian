<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

/**
 * This tool helps you to find an effect on some components:
 *
 * * The bottleneck effect
 * Do you remeber this project where everytime you made a change somewhere,
 * THAT class need to be modified too ? The "dependRank" method finds this
 * kind of problems. It searchs for the depencencies, but not only direct
 * dependencies but also the combination of dependencies accross all the
 * vertices of a digraph. My recomandation : abstract this component first :
 * make multiple interfaces, explode it with strategy pattern, decorator,
 * CoR etc... All bugs are "drown" to this component like a blackhole.
 */
class DependCentrality extends Centrality
{

    /**
     * Add dependency centrality information on each vertex
     * (edge effect on this digraph)
     */
    public function decorate()
    {
        $this->addCentralityRank($this->graph, 'centrality');
    }

}