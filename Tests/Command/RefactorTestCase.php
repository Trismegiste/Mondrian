<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * RefactorTestCase is a test template for refactoring Command
 */
abstract class RefactorTestCase extends \PHPUnit_Framework_TestCase
{

    protected $application;
    protected $cmdName;

    protected function setUp()
    {
        $this->application = new Application();
        $command = $this->createCommand();
        $this->cmdName = $command->getName();
        $this->application->add($command);
    }

    abstract protected function createCommand();

    protected function commonExecute()
    {
        $command = $this->application->find($this->cmdName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'dir' => __DIR__ . '/../Fixtures/Project'
        ));

        // return the output for further tests
        return $commandTester->getDisplay();
    }

}
