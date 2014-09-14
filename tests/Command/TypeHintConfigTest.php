<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\TypeHintConfig;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * TypeHintConfigTest is a func test for TypeHintConfig
 */
class TypeHintConfigTest extends TestTemplate
{

    protected function createCommand()
    {
        return new TypeHintConfig();
    }

    public function testOutput()
    {
        $command = $this->application->find($this->cmdName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'dir' => __DIR__ . '/../Fixtures/Refact/',
            '--dry' => true
        ));
    }

}
