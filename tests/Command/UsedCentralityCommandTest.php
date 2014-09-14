<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\UsedCentralityCommand;

/**
 * UsedCentralityCommandTest is a unit test for UsedCentralityCommand
 */
class UsedCentralityCommandTest extends TestTemplate
{

    protected function createCommand()
    {
        return new UsedCentralityCommand();
    }

    public function testExecute()
    {
        $this->commonExecute();
    }

}
