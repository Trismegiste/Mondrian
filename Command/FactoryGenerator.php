<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Command\Command;
use Trismegiste\Mondrian\Refactor\FactoryGenBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Trismegiste\Mondrian\Builder\Linking;
use Trismegiste\Mondrian\Builder\Statement\Builder;
use Trismegiste\Mondrian\Parser\PhpDumper;

/**
 * FactoryGenerator is a refactoring tools which scans all new statements
 * and create a protected method for each.
 * 
 * With this, it is possible to mockup the new instance for unit testing
 */
class FactoryGenerator extends Command
{

    protected function configure()
    {
        $this->setName('refactor:factory')
                ->addArgument('file', InputArgument::REQUIRED, 'The source file to refactor')
                ->setDescription('Scans a file and replace new instances in methods by protected factories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('file');

        $compil = new Linking(
                new Builder(), new FactoryGenBuilder(new PhpDumper()));

        $compil->run(new \ArrayIterator(array($source)));
    }

}