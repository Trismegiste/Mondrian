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
use Trismegiste\Mondrian\Refactor\Contractor;

/**
 * RefactorCommand recursively scans a directory and
 * refactors concrete class with annotations.
 * 
 * It creates an interface, changes paramters types and adds inheritance
 */
class RefactorCommand extends Command
{

    protected function configure()
    {
        $this
                ->setName('mondrian:abstract')
                ->setDescription('Scans a directory and refactors classes with annotations')
                ->addArgument('dir', InputArgument::REQUIRED, 'The directory to explore')
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

        $service = new Contractor();
        $service->refactor($listing);
    }

}