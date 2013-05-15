<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Analysis\DependCentrality;

/**
 * DependCentralityCommand transforms a bunch of php files into a digraph
 * and exports it into a report file with centrality informations of
 * the dependencies of each node.
 *
 * Higher rank means the vertex has many directed edges pointing to other
 * vertices. It means the vertex has a higher risk to be changed
 * each time there is a change somewhere in the source code (bottleneck effect)
 */
class DependCentralityCommand extends AbstractCentrality
{

    protected function getAlgorithm()
    {
        return 'bottleneck';
    }

    protected function createCentrality(Graph $g)
    {
        return new DependCentrality($g);
    }

}
