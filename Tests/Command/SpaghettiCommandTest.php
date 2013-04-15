<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\SpaghettiCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * SpaghettiCommandTest is a unit test for SpaghettiCommand
 */
class SpaghettiCommandTest extends TestTemplate
{

    protected function createCommand()
    {
        return new SpaghettiCommand();
    }

    public function testExecute()
    {
        $this->commonExecute();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage loose is not a valid strategy
     */
    public function testBadParameter()
    {
        $command = $this->application->find($this->cmdName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'dir' => __DIR__ . 'nihil',
            '--strategy' => 'loose'
        ));
    }

}