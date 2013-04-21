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
                ->setDescription('scans a directory and refactors classes with annotations')
                ->addArgument('dir', InputArgument::REQUIRED, 'The directory to explore');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('dir');

        $listing = array();
        $scan = new Finder();
        $scan->files()->in($directory)->name('*.php')->exclude($ignoreDir);
        foreach ($scan as $fch) {
            $listing[] = (string) $fch->getRealPath();
        }

        $transformer = new Contractor();
        $graph = $transformer->refactor($listing);
    }

}