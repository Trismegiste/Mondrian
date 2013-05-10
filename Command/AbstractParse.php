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
use Trismegiste\Mondrian\Config\Helper;

/**
 * AbstractParse transforms a bunch of php files into a digraph
 * and exports it into a report file
 *
 * Design pattern : Template Method
 */
abstract class AbstractParse extends Command
{

    abstract protected function getSubname();

    protected function getFullDesc()
    {
        return 'Parses a directory to generate a digraph';
    }

    /**
     * The method that does the job : it computes/decorates/redifines the
     * graph passed in parameter.
     *
     * @param Graph $g the graph to process
     * @param OutputInterface $out console output
     *
     * @return Graph the processed graph (the same or another)
     */
    abstract protected function processGraph(Graph $g, OutputInterface $out);

    protected function configure()
    {
        $this
                ->setName('mondrian:' . $this->getSubname())
                ->setDescription($this->getFullDesc())
                ->addArgument('dir', InputArgument::REQUIRED, 'The directory to explore')
                ->addArgument('report', InputArgument::OPTIONAL, 'The filename of the report', 'report')
                ->addOption('ignore', 'i', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to ignore', array('Tests', 'vendor'))
                ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Format of export', 'dot');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('dir');
        $reportName = $input->getArgument('report');
        $ignoreDir = $input->getOption('ignore');
        $ext = $input->getOption('format');

        $scan = new Finder();
        $scan->files()
                ->in($directory)
                ->name('*.php')
                ->exclude($ignoreDir);

        $transformer = new Grapher($this->getConfig($directory));
        $output->writeln(sprintf("Parsing %d files...", $scan->count()));
        $graph = $transformer->build($scan->getIterator());

        $output->writeln(sprintf("Processing digraph with %d vertices and %d edges...", count($graph->getVertexSet()), count($graph->getEdgeSet())));
        $processed = $this->processGraph($graph, $output);

        $ff = new Factory();
        $dumper = $ff->create($processed, $ext);
        $reportName = "$reportName.$ext";
        file_put_contents($reportName, $dumper->export());
        $output->writeln("Report $reportName created");
    }

    /**
     * get the graph section of the configuration for this package
     *
     * @param string $dir the root dir of the package
     *
     * @return array
     */
    protected function getConfig($dir)
    {
        $helper = new Helper();

        return $helper->getGraphConfig($dir);
    }

}