<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Symfony\Component\Console\Input\InputOption;
use Trismegiste\Mondrian\Analysis\CouplingMaker;

/**
 * BadInterfaceCommand transforms reduced the digraph
 * to the bad interface with concrete parameters in their
 * methods
 */
class BadInterfaceCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'badcontract';
    }

    protected function getFullDesc()
    {
        return parent::getFullDesc() . ' with bad interfaces';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = new CouplingMaker($graph);
        $result = $algo->createReducedGraph();

        return $result;
    }

}