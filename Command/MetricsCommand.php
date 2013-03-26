<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Trismegiste\Mondrian\Analysis\CodeMetrics;
use Trismegiste\Mondrian\Transform\Grapher;

/**
 * MetricsCommand transforms a bunch of php files into a digraph
 * and make some code metrics about it
 */
class MetricsCommand extends Command
{

    protected function configure()
    {
        $this
                ->setName('mondrian:metrics')
                ->setDescription('Code metrics of source code')
                ->addArgument('dir', InputArgument::OPTIONAL, 'The directory to explore', './src')
//                ->addArgument('report', InputArgument::OPTIONAL, 'The filename of the report', 'report')
                ->addOption('ignore', 'i', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to ignore', array('Tests', 'vendor'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('dir');
        $ignoreDir = $input->getOption('ignore');

        $listing = array();
        $scan = new Finder();
        $scan->files()->in($directory)->name('*.php')->exclude($ignoreDir);
        foreach ($scan as $fch) {
            $listing[] = (string) $fch->getRealPath();
        }

        $transformer = new Grapher();
        $graph = $transformer->parse($listing);

        $stat = new CodeMetrics($graph);
        print_r($stat->getCardinal());

        $most = $stat->getMostDepending();
        print_r($most);
    }

}