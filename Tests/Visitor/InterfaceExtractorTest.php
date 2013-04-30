<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\InterfaceExtractor;

/**
 * InterfaceExtractorTest tests for InterfaceExtractor
 */
class InterfaceExtractorTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;
    protected $context;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Trismegiste\Mondrian\Refactor\Refactored')
                ->getMock();
        $this->visitor = new InterfaceExtractor($this->context);
    }

    public function testNonTypedParam()
    {

    }

}