<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\ParamRefactor;

/**
 * ParamRefactorTest is a test for ParamRefactor
 */
class ParamRefactorTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;
    protected $context;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Trismegiste\Mondrian\Refactor\Refactored')
                ->getMock();
        $this->visitor = new ParamRefactor($this->context);
    }

}
