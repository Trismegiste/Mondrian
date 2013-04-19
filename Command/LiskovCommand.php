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
 * to the LSP violation to refactor (and later achieve ISP)
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
        return parent::getFullDesc() . ' with LSP violation';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = new LiskovSearch($graph);
        $result = $algo->createReducedGraph();
        $central = new \Trismegiste\Mondrian\Analysis\UsedCentrality($result);
        $central->decorate();

        return $result;
    }

}