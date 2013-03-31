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
 * AbstractCentrality transforms a bunch of php files into a digraph
 * and exports it into a report file with centrality informations
 *
 */
abstract class AbstractCentrality extends AbstractParse
{

    abstract protected function getAlgorithm();

    protected function getSubname()
    {
        return 'centrality:' . $this->getAlgorithm();
    }

    protected function getFullDesc()
    {
        return 'Transforms a bunch of php file into a digraph with centrality informations';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = new Centrality($graph);
        $this->processCentrality($algo);

        return $graph;
    }

    abstract protected function processCentrality(Centrality $g);
}