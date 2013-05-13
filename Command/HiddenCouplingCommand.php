<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Symfony\Component\Console\Input\InputOption;
use Trismegiste\Mondrian\Analysis\HiddenCoupling;

/**
 * HiddenCouplingCommand transforms a bunch of php files into a digraph
 * with hidden coupling
 *
 */
class HiddenCouplingCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'hidden';
    }

    protected function getFullDesc()
    {
        return parent::getFullDesc() . ' with hidden coupling';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = new HiddenCoupling($graph);
        $result = $algo->createReducedGraph();

        return $result;
    }

}
