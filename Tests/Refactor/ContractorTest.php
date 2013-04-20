<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refacotr;

use Trismegiste\Mondrian\Refactor\Contractor;

/**
 * ContractorTest is test for Contractor
 *
 */
class ContractorTest extends \PHPUnit_Framework_TestCase
{

    protected $coder;

    protected function setUp()
    {
        $this->coder = new Contractor();
    }

    public function testParse()
    {
        $iter = array(__DIR__ . '/../Fixtures/Refact/Earth.php',
            __DIR__ . '/../Fixtures/Refact/Moon.php');
        $this->coder->parse($iter);
    }

}