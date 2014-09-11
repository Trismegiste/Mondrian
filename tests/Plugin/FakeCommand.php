<?php

namespace Trismegiste\Mondrian\Tests\Plugin;

use Symfony\Component\Console\Command\Command;

/**
 * FakeCommand is a fake command for test
 */
class FakeCommand extends Command
{

    public function __construct()
    {
        parent::__construct('fake');
    }

}