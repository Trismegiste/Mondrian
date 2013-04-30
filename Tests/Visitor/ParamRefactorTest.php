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

    public function testNonTypedParam()
    {
        $node = new \PHPParser_Node_Param('obj');
        $this->context->expects($this->never())
                ->method('hasNewContract');
        $this->visitor->enterNode($node);
    }

    public function testTypedParam()
    {
        $node = new \PHPParser_Node_Param('obj', null, 'array');
        $this->context->expects($this->never())
                ->method('hasNewContract');
        $this->visitor->enterNode($node);
    }

    public function testClassTypedParamWithName()
    {
        $node = new \PHPParser_Node_Param('obj', null, new \PHPParser_Node_Name('SplObjectStorage'));
        $this->context->expects($this->once())
                ->method('hasNewContract')
                ->with('SplObjectStorage')
                ->will($this->returnValue(true));

        $this->visitor->enterNode($node);
    }

    public function testClassTypedParamWithFqcn()
    {
        $node = new \PHPParser_Node_Param('obj', null, new \PHPParser_Node_Name_FullyQualified('Pull\Me\Under'));
        $this->context->expects($this->once())
                ->method('hasNewContract')
                ->with('Pull\Me\Under')
                ->will($this->returnValue(true));

        $this->visitor->enterNode($node);
    }

}
