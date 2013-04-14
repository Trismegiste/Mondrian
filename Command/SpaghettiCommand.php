<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Symfony\Component\Console\Input\InputOption;
use Trismegiste\Mondrian\Analysis\SpaghettiCoupling;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Analysis\Strategy;

/**
 * SpaghettiCommand reduces a graph to its coupled implementation vertices
 */
class SpaghettiCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'spaghetti';
    }

    protected function getFullDesc()
    {
        return parent::getFullDesc() . ' with spaghetti coupling';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = new SpaghettiCoupling($graph);
        $result = new Digraph();
        $algo->setFilterPath(new Strategy\ByCalling($result));
        $algo->generateCoupledClassGraph();

        return $result;
    }

}