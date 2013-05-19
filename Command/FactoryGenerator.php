<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Trismegiste\Mondrian\Refactor\FactoryGenBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Builder\Linking;
use Trismegiste\Mondrian\Builder\Statement\Builder;
use Trismegiste\Mondrian\Parser\PhpDumper;

/**
 * FactoryGenerator is a refactoring tools which scans all new statements
 * and create a protected method for each.
 * 
 * With this, it is possible to mockup the new instance for unit testing
 */
class FactoryGenerator extends RefactorCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('refactor:factory')
                ->setDescription('Scans a source code and replace new instances by factories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compil = new Linking(
                new Builder(), new FactoryGenBuilder(new PhpDumper()));

        $compil->run($this->phpfinder->getIterator());
    }

}