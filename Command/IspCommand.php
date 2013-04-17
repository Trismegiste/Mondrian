<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Symfony\Component\Console\Input\InputOption;
use Trismegiste\Mondrian\Analysis\IspSearch;

/**
 * IspCommand transforms a bunch of php files into a reduced digraph
 * to the LSP violation to refactor and achieve ISP
 *
 */
class IspCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'isp';
    }

    protected function getFullDesc()
    {
        return parent::getFullDesc() . ' with ISP violation';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = new IspSearch($graph);
        $result = $algo->generateIspGraph();

        return $result;
    }

}