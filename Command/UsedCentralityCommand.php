<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Trismegiste\Mondrian\Analysis\Centrality;

/**
 * UsedCentralityCommand transforms a bunch of php files into a digraph
 * and exports it into a report file with centrality informations of
 * the using of each node.
 * 
 * Higher rank means the vertex has many directed edges toward it 
 * (he is the target). It means each time there is a change in the vertex 
 * there are many effects accross the source code (a.k.a the ripple effect)
 */
class UsedCentralityCommand extends AbstractCentrality
{

    protected function getAlgorithm()
    {
        return 'used';
    }

    protected function processCentrality(Centrality $algo)
    {
        $algo->addUsedRank();
    }

}