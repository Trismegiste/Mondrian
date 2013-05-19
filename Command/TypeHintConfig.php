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
use Symfony\Component\Finder\Finder;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Config\Helper;
use Trismegiste\Mondrian\Transform\Logger\GraphLogger;
use Symfony\Component\Yaml\Yaml;

/**
 * TypeHintConfig generates a default type-hint config files
 * 
 * Overwrites any previous existing
 */
class TypeHintConfig extends Command
{

    protected $fineTuning;
    protected $phpfinder;
    protected $newConfigFile;

    protected function configure()
    {
        $this
                ->setName('typehint:config')
                ->setDescription('Regenerates and overwrites any existing typehint config at the root of the package')
                ->addArgument('dir', InputArgument::REQUIRED, 'The directory to explore')
                ->addOption('ignore', 'i', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to ignore', array('Tests', 'vendor'));
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $graph = new Digraph();
        $logger = new GraphLogger();
        $compil = new Linking(new Builder(), new GraphBuilder($this->fineTuning, $graph, $logger));

        $output->writeln(sprintf("Parsing %d files...", $this->phpfinder->count()));
        $compil->run($this->phpfinder->getIterator());

        file_put_contents($this->newConfigFile, Yaml::dump($logger->getDigest(), 5));
        $output->writeln("Default config {$this->newConfigFile} created");
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
        $this->newConfigFile = $directory . '/.mondrian.yml';
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
