<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Symfony\Component\Console\Input\InputOption;
use Trismegiste\Mondrian\Analysis\SpaghettiCoupling;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Analysis\Strategy;
use Trismegiste\Mondrian\Analysis\Cycle;

/**
 * SpaghettiCommand reduces a graph to its coupled implementation vertices
 */
class SpaghettiCommand extends AbstractParse
{

    protected $showCycle = false;
    protected $connectionStrategy = null;

    protected function getSubname()
    {
        return 'spaghetti';
    }

    protected function getFullDesc()
    {
        return parent::getFullDesc() . ' with spaghetti coupling';
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption('cycle', null, InputOption::VALUE_NONE, 'Show cycles between classes');
        $this->addOption('strategy', null, InputOption::VALUE_REQUIRED, 'Select the strategy of connection [call|concrete|any]', 'call');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->showCycle = $input->hasOption('cycle');
        $strategy = $input->getOption('strategy');
        $this->connectionStrategy = $this->getClassFor($strategy);
    }

    private function getClassFor($strategy)
    {
        $typeList = array(
            'call' => 'Trismegiste\Mondrian\Analysis\Strategy\ByCalling',
            'concrete' => 'Trismegiste\Mondrian\Analysis\Strategy\ByImplemented',
            'any' => 'Trismegiste\Mondrian\Analysis\Strategy\ByConnection'
        );
        if (array_key_exists($strategy, $typeList)) {
            return $typeList[$strategy];
        } else {
            throw new \RuntimeException("$strategy is not a valid strategy");
        }
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $algo = new SpaghettiCoupling($graph);
        $result = new Digraph();
        $chosenStrategy = $this->connectionStrategy;
        $algo->setFilterPath(new $chosenStrategy($result));
        $algo->generateCoupledClassGraph();

        if ($this->showCycle) {
            $result = new Cycle($result);
        }

        return $result;
    }

}
