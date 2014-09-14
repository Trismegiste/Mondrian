<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\DependCentralityCommand;

/**
 * DependCentralityCommandTest is a unit test for DependCentralityCommand
 */
class DependCentralityCommandTest extends TestTemplate
{

    protected function createCommand()
    {
        return new DependCentralityCommand();
    }

    public function testExecute()
    {
        $this->commonExecute();
    }

}
