<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;

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
        return parent::getFullDesc() . ' with centrality informations';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = $this->createCentrality($graph);
        $algo->decorate();

        return $graph;
    }

    /**
     * @return Centrality
     */
    abstract protected function createCentrality(Graph $g);
}