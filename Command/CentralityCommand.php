<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Symfony\Component\Console\Input\InputOption;
use Trismegiste\Mondrian\Analysis\Centrality;

/**
 * CentralityCommand transforms a bunch of php files into a digraph
 * and exports it into a report file with centrality informations
 *
 */
class CentralityCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'centrality';
    }

    protected function getFullDesc()
    {
        return 'Transforms a bunch of php file into a digraph with centrality informations';
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption('algo', null, InputOption::VALUE_REQUIRED, 'Algorithm used for centrality calculus [used|depend]', 'used');
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = new Centrality($graph);
        $algo->addDependRank();

        return $graph;
    }

}