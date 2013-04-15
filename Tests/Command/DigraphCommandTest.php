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
class DigraphCommandTest extends TestTemplate
{

    protected function createCommand()
    {
        return new DigraphCommand();
    }

    public function testOutput()
    {
        $out = $this->commonExecute();
        $this->assertEquals(1, preg_match('#declared in interfaces#', $out));
    }

}