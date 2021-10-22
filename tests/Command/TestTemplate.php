<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * TestTemplate is a test template for Command
 */
abstract class TestTemplate extends \PHPUnit\Framework\TestCase
{

    protected $application;
    protected $cmdName;

    protected function setUp():void
    {
        $this->application = new Application();
        $command = $this->createCommand();
        $this->cmdName = $command->getName();
        $this->application->add($command);
    }

    abstract protected function createCommand();

    protected function commonExecute()
    {
        $fch = tempnam(sys_get_temp_dir(), 'graph');

        $command = $this->application->find($this->cmdName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'dir' => __DIR__ . '/../Fixtures/Project',
            'report' => $fch
        ));
        // test the generated graphviz file
        $ptr = fopen($fch . '.dot', 'r');
        $heading = fgets($ptr);
        $this->assertStringStartsWith('digraph', $heading);
        fclose($ptr);

        // return the output for further tests
        return $commandTester->getDisplay();
    }

}
