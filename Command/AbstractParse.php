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
use Trismegiste\Mondrian\Transform\Grapher;
use Trismegiste\Mondrian\Transform\Format\Factory;
use Symfony\Component\Finder\Finder;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * AbstractParse transforms a bunch of php files into a digraph
 * and exports it into a report file
 */
abstract class AbstractParse extends Command
{

    abstract protected function getSubname();

    abstract protected function getFullDesc();

    abstract protected function processGraph(Graph $g, OutputInterface $out);

    protected function configure()
    {
        $this
                ->setName('mondrian:' . $this->getSubname())
                ->setDescription($this->getFullDesc())
                ->addArgument('dir', InputArgument::OPTIONAL, 'The directory to explore', './src')
                ->addArgument('report', InputArgument::OPTIONAL, 'The filename of the report', 'report')
                ->addOption('ignore', 'i', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to ignore', array('Tests', 'vendor'))
                ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Format of export', 'dot');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('dir');
        $reportName = $input->getArgument('report');
        $ignoreDir = $input->getOption('ignore');
        $ext = $input->getOption('format');

        $listing = array();
        $scan = new Finder();
        $scan->files()->in($directory)->name('*.php')->exclude($ignoreDir);
        foreach ($scan as $fch) {
            $listing[] = (string) $fch->getRealPath();
        }

        $transformer = new Grapher();
        $graph = $transformer->parse($listing);

        $processed = $this->processGraph($graph, $output);

        if (!is_null($processed)) {
            $ff = new Factory();
            $dumper = $ff->create($processed, $ext);
            file_put_contents("$reportName.$ext", $dumper->export());
        }
    }

}