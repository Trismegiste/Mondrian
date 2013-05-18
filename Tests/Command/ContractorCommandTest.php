<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Command;

use Trismegiste\Mondrian\Command\ContractorCommand;

/**
 * ContractorCommandTest tests the contractor command
 */
class ContractorCommandTest extends RefactorTestCase
{

    protected function createCommand()
    {
        return new ContractorCommand();
    }

    public function testExecute()
    {
        $this->commonExecute();
    }

}