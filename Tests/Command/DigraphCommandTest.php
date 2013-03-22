<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\DigraphCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * DigraphCommandTest is a unit test for DigraphCommand
 */
class DigraphCommandTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->application = new Application();
        $command = new DigraphCommand();
        $this->application->add($command);
    }

    public function testExecute()
    {
        $fch = tempnam(sys_get_temp_dir(), 'graph');

        $command = $this->application->find('mondrian:digraph');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'dir' => __DIR__ . '/../Fixtures/Project',
            'report' => $fch
        ));

        $ptr = fopen($fch, 'r');
        $heading = fgets($ptr);
        $this->assertStringStartsWith('digraph', $heading);
        fclose($ptr);
    }

}