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

    public function testInit()
    {
        $this->assertFalse($this->visitor->isModified());
        $this->assertFalse($this->visitor->hasGenerated());
    }

    public function getSimpleClass()
    {
        return array(array(
                new \PHPParser_Node_Stmt_Class('Systematic', array(), array(
                    'comments' => array(
                        new \PHPParser_Comment('@mondrian contractor Chaos')
                    )
                ))
                ));
    }

    /**
     * @dataProvider getSimpleClass
     */
    public function testStackingMethod($node)
    {
        $this->assertAttributeEquals(false, 'newInterface', $this->visitor);
        $this->visitor->enterNode($node);
        $this->assertAttributeNotEquals(false, 'newInterface', $this->visitor);
    }

    /**
     * @dataProvider getSimpleClass
     */
    public function testNoGeneration($node)
    {
        $this->assertAttributeEquals(false, 'newInterface', $this->visitor);
        $this->visitor->leaveNode($node);
    }

    /**
     * @dataProvider getSimpleClass
     */
    public function testGeneration($node)
    {
        $this->visitor->enterNode($node);
        $this->visitor->leaveNode($node);
        $this->assertTrue($this->visitor->hasGenerated());
        $generated = $this->visitor->getGenerated();
        $this->assertArrayHasKey('Chaos', $generated);
        $generated = $generated['Chaos'];
        $this->assertCount(2, $generated);
        $this->assertInstanceOf('\PHPParser_Node_Stmt_Namespace', $generated[0]);
        $this->assertInstanceOf('\PHPParser_Node_Stmt_Interface', $generated[1]);
    }

}