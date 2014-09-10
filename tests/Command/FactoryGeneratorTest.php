<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\FactoryGenerator;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * FactoryGeneratorTest tests the command factory generator
 */
class FactoryGeneratorTest extends RefactorTestCase
{

    protected function createCommand()
    {
        return new FactoryGenerator();
    }

    public function testDryRun()
    {
        $command = $this->application->find($this->cmdName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'file' => __DIR__ . '/../Fixtures/Refact/ForFactory.php',
            '--dry' => true
        ));
    }

}