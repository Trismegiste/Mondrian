<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\NewContractCollector;

/**
 * NewContractCollectorTest is a test for NewContractCollector
 */
class NewContractCollectorTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;

    protected function setUp()
    {
        $this->visitor = new NewContractCollector();
    }

}