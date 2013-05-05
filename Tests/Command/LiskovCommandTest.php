<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\LiskovCommand;

/**
 * LiskovCommandTest is a unit test for LiskovCommand
 */
class LiskovCommandTest extends TestTemplate
{

    protected function createCommand()
    {
        return new LiskovCommand();
    }

    public function testExecute()
    {
        $this->commonExecute();
    }

}