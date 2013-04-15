<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\HiddenCouplingCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * HiddenCouplingCommandTest is a unit test for HiddenCouplingCommand
 */
class HiddenCouplingCommandTest extends TestTemplate
{

    protected function createCommand()
    {
        return new HiddenCouplingCommand();
    }

    public function testExecute()
    {
        $this->commonExecute();
    }

}