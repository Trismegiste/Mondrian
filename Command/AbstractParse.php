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
use Trismegiste\Mondrian\Builder\Linking;
use Trismegiste\Mondrian\Builder\Statement\Builder;
use Trismegiste\Mondrian\Transform\GraphBuilder;
use Trismegiste\Mondrian\Transform\Format\Factory;
use Symfony\Component\Finder\Finder;
use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Config\Helper;
use Trismegiste\Mondrian\Transform\Logger\NullLogger;

/**
 * AbstractParse transforms a bunch of php files into a digraph
 * and exports it into a report file
 *
 * Design pattern : Template Method
 */
abstract class AbstractParse extends Command
{

    protected $fineTuning;
    protected $phpfinder;
    protected $reportName;
    protected $reportFormat;

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
                ->setName($this->getSubname())
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
        $graph = new Digraph();
        $logger = new NullLogger();
        $compil = new Linking(new Builder(), new GraphBuilder($this->fineTuning, $graph, $logger));

        $output->writeln(sprintf("Parsing %d files...", $this->phpfinder->count()));
        $compil->run($this->phpfinder->getIterator());

        $output->writeln(sprintf("Processing digraph with %d vertices and %d edges...", count($graph->getVertexSet()), count($graph->getEdgeSet())));
        $processed = $this->processGraph($graph, $output);

        $ff = new Factory();
        $dumper = $ff->create($processed, $this->reportFormat);
        file_put_contents($this->reportName, $dumper->export());
        $output->writeln("Report $this->reportName created");
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

    /**
     * Inject parameters of the command
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('dir');
        $ignoreDir = $input->getOption('ignore');
        $this->fineTuning = $this->getConfig($directory);
        $this->phpfinder = $this->getPhpFinder($directory, $ignoreDir);

        $this->reportName = $input->getArgument('report');
        $this->reportFormat = $input->getOption('format');
        $this->reportName = "$this->reportName.$this->reportFormat";
    }

    protected function getPhpFinder($directory, $ignoreDir)
    {
        $scan = new Finder();
        $scan->files()
                ->in($directory)
                ->name('*.php')
                ->exclude($ignoreDir);

        return $scan;
    }

}
