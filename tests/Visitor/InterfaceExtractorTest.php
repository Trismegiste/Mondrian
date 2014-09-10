<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\InterfaceExtractor;
use Trismegiste\Mondrian\Parser\PhpFile;

/**
 * InterfaceExtractorTest tests for InterfaceExtractor
 */
class InterfaceExtractorTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;
    protected $context;
    protected $dumper;

    protected function setUp()
    {
        $this->dumper = $this->getMockForAbstractClass('Trismegiste\Mondrian\Parser\PhpPersistence');
        $this->context = $this->getMockBuilder('Trismegiste\Mondrian\Refactor\Refactored')
                ->getMock();
        $this->visitor = new InterfaceExtractor($this->context, $this->dumper);
    }

    public function getSimpleClass()
    {
        return array(array(
                new \PHPParser_Node_Stmt_Class('Systematic', array(), array(
                    'comments' => array(new \PHPParser_Comment('@mondrian contractor Chaos'))
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
        $this->dumper->expects($this->never())
                ->method('write')
                ->will($this->returnCallback(array($this, 'stubbedTestedWrite')));

        $this->assertAttributeEquals(false, 'newInterface', $this->visitor);
        $this->visitor->beforeTraverse(array());
        $this->assertAttributeEquals(array(), 'newContent', $this->visitor);
        // start traversing but not enter in class
        $this->visitor->leaveNode($node);
        // does not generate a write
        $this->visitor->afterTraverse(array());
    }

    public function testDoNothingForCC()
    {
        $node = new \PHPParser_Node_Stmt_Interface('Dummy', array(
            'stmts' => new \PHPParser_Node_Stmt_ClassMethod('dummy')
        ));
        $this->visitor->beforeTraverse(array($node));
        $this->visitor->enterNode($node);
    }

    /**
     * @dataProvider getSimpleClass
     */
    public function testGeneration($node)
    {
        $this->dumper->expects($this->once())
                ->method('write')
                ->will($this->returnCallback(array($this, 'stubbedTestedWrite')));

        $this->visitor->enterNode(new \Trismegiste\Mondrian\Parser\PhpFile('/addicted/to/Systematic.php', array()));
        $this->visitor->enterNode($node);
        $this->visitor->enterNode(new \PHPParser_Node_Stmt_ClassMethod('forsaken'));
        $this->visitor->leaveNode($node);
        $this->visitor->afterTraverse(array());

        $this->assertAttributeNotEmpty('newContent', $this->visitor);
    }

    public function stubbedTestedWrite(PhpFile $file)
    {
        $this->assertEquals('/addicted/to/Chaos.php', $file->getRealPath());
        $generated = $file->getIterator();
        $this->assertCount(2, $generated);
        $this->assertInstanceOf('\PHPParser_Node_Stmt_Namespace', $generated[0]);
        $this->assertInstanceOf('\PHPParser_Node_Stmt_Interface', $generated[1]);
        $interf = $generated[1]->stmts;
        $this->assertInstanceOf('\PHPParser_Node_Stmt_ClassMethod', $interf[0]);
        $this->assertEquals('forsaken', $interf[0]->name);
    }

    /**
     * @dataProvider getSimpleClass
     * @expectedException RuntimeException
     */
    public function testBadUseOfVisitor($node)
    {
        $this->visitor->enterNode($node);
        $this->visitor->leaveNode($node);
    }

}
