<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Plugin;

use Trismegiste\Mondrian\Plugin;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * AboutTest tests the About command
 */
class AboutTest extends \PHPUnit_Framework_TestCase
{

    protected $application;
    protected $cmdName;

    protected function setUp()
    {
        $this->application = new Plugin\Application();
        $command = new Plugin\About();
        $this->cmdName = $command->getName();
        $this->application->add($command);
    }

    public function testOutput()
    {
        $command = $this->application->find($this->cmdName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName()
        ));

        // return the output for further tests
        $out = $commandTester->getDisplay();
        $this->assertRegExp('#trismegiste#', $out);
    }

}