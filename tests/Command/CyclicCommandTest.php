<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\CyclicCommand;

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
