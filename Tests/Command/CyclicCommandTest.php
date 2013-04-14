<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\CyclicCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * CyclicCommandTest is a unit test for CyclicCommand
 */
class CyclicCommandTest extends TestTemplate
{

    protected function createCommand()
    {
        return new CyclicCommand();
    }

    public function testExecute()
    {
        $this->commonExecute();
    }

}