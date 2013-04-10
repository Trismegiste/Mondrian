<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Graph\Tarjan;

/**
 * CyclicCommand reduces a graph to its strongly connected components
 */
class CyclicCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'scc';
    }

    protected function getFullDesc()
    {
        return 'Finds cyclic coupling';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $default = array('fixedsize' => true, 'width' => 2, 'height' => 2);
        $dot = new \Alom\Graphviz\Digraph('G');

        $inverseIndex = new \SplObjectStorage();
        foreach ($graph->getVertexSet() as $idx => $vertex) {
            $inverseIndex[$vertex] = $idx;
            $dot->node($idx, array_merge($default, $vertex->getAttribute()));
        }
        foreach ($graph->getEdgeSet() as $edge) {
            $dot->edge(array($inverseIndex[$edge->getSource()], $inverseIndex[$edge->getTarget()]));
        }

        $algo = new Tarjan($graph);
        foreach ($algo->getStronglyConnected() as $idx => $subgraph) {
            if (count($subgraph) > 2) {
                $scc = $dot->subgraph("cluster_$idx");
                foreach ($subgraph as $vertex) {
                    $scc->node($inverseIndex[$vertex]);
                }
                $scc->end();
            }
        }

        file_put_contents("wesh.dot", $dot->render());

        return null;
    }

}