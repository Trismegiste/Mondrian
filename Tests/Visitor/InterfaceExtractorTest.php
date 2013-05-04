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
        $this->assertAttributeEquals(false, 'newInterface', $this->visitor);
        $this->visitor->leaveNode($node);
    }

    public function testDoNothingForCC()
    {
        $node = new \PHPParser_Node_Stmt_Interface('Dummy', array(
            'stmts' => new \PHPParser_Node_Stmt_ClassMethod('dummy')
        ));
        $this->visitor->beforeTraverse(array($node));
        $this->visitor->enterNode($node);
        $this->assertFalse($this->visitor->isModified());
        $this->assertFalse($this->visitor->hasGenerated());
    }

    /**
     * @dataProvider getSimpleClass
     */
    public function testGeneration($node)
    {
        $this->visitor->enterNode(new \Trismegiste\Mondrian\Parser\PhpFile('/addicted/to/Systematic.php', array()));
        $this->visitor->enterNode($node);
        $this->visitor->enterNode(new \PHPParser_Node_Stmt_ClassMethod('forsaken'));
        $this->visitor->leaveNode($node);

        $this->assertTrue($this->visitor->hasGenerated());
        $generated = $this->visitor->getGenerated();

        $this->assertCount(1, $generated);
        $newFile = $generated[0];
        $this->assertInstanceOf('Trismegiste\Mondrian\Parser\PhpFile', $newFile);
        $this->assertEquals('/addicted/to/Chaos.php', $newFile->getRealPath());

        $generated = $newFile->getIterator();
        $this->assertCount(2, $generated);
        $this->assertInstanceOf('\PHPParser_Node_Stmt_Namespace', $generated[0]);
        $this->assertInstanceOf('\PHPParser_Node_Stmt_Interface', $generated[1]);
        $interf = $generated[1]->stmts;
        $this->assertInstanceOf('\PHPParser_Node_Stmt_ClassMethod', $interf[0]);
        $this->assertEquals('forsaken', $interf[0]->name);
    }

}