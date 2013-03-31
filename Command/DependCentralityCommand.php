<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Trismegiste\Mondrian\Analysis\Centrality;

/**
 * DependCentralityCommand transforms a bunch of php files into a digraph
 * and exports it into a report file with centrality informations of
 * the dependencies of each node.
 * 
 * Higher rank means the vertex has many directed edges pointing to other 
 * vertices. It means the vertex has a higher risk to be changed 
 * each time there is a change accross the source code (bottleneck effect)
 */
class DependCentralityCommand extends AbstractCentrality
{

    protected function getAlgorithm()
    {
        return 'depend';
    }

    protected function processCentrality(Centrality $algo)
    {
        $algo->addDependRank();
    }

}