<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\BadInterfaceCommand;

/**
 * BadInterfaceCommandTest is a unit test for BadInterfaceCommand
 */
class BadInterfaceCommandTest extends TestTemplate
{

    protected function createCommand()
    {
        return new BadInterfaceCommand();
    }

    public function testExecute()
    {
        $this->commonExecute();
    }

}