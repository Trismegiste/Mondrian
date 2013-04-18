<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Analysis\LiskovSearch;

/**
 * LiskovCommand transforms a bunch of php files into a reduced digraph
 * to the LSP violation to refactor and achieve ISP
 *
 */
class LiskovCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'liskov';
    }

    protected function getFullDesc()
    {
        return parent::getFullDesc() . ' with ISP violation';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = new LiskovSearch($graph);
        $result = $algo->generateIspGraph();
        $central = new \Trismegiste\Mondrian\Analysis\Centrality($result);
        $central->addUsedRank();

        return $result;
    }

}