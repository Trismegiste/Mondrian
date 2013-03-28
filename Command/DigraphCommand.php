<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * DigraphCommand transforms a bunch of php files into a digraph
 * and exports it into a report file
 *
 */
class DigraphCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'digraph';
    }

    protected function getFullDesc()
    {
        return 'Transforms a bunch of php file into a digraph';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        return $graph;
    }

}