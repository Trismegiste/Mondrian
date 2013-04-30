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
    protected $context;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Trismegiste\Mondrian\Refactor\Refactored')
                ->getMock();
        $this->visitor = new NewContractCollector($this->context);
    }

    protected function buildClassNode()
    {
        $node = $this->getMockBuilder('PHPParser_Node_Stmt_Class')
                ->disableOriginalConstructor()
                ->getMock();

        $node->expects($this->once())
                ->method('getType')
                ->will($this->returnValue('Stmt_Class'));

        return $node;
    }

    public function testInitialization()
    {
        $this->visitor->beforeTraverse(array());
        $this->assertFalse($this->visitor->isModified());
        $this->assertFalse($this->visitor->hasGenerated());
    }

    public function testEnterClassWithoutComments()
    {
        $node = $this->buildClassNode();

        $node->expects($this->exactly(2))
                ->method('hasAttribute')
                ->will($this->returnValueMap(array(
                            array('comments', false),
                            array('contractor', false)
        )));

        $this->context->expects($this->never())
                ->method('pushNewContract');

        $this->visitor->enterNode($node);
    }

    public function testEnterCommentedClassWithoutAnnotations()
    {
        $node = $this->buildClassNode();

        $node->expects($this->exactly(2))
                ->method('hasAttribute')
                ->will($this->returnValueMap(array(
                            array('comments', true),
                            array('contractor', false)
        )));

        $node->expects($this->once())
                ->method('getAttribute')
                ->with('comments')
                ->will($this->returnValue(array(new \PHPParser_Comment('Some useless comments'))));

        $this->context->expects($this->never())
                ->method('pushNewContract');

        $this->visitor->enterNode($node);
    }

    public function testEnterAnnotedClass()
    {
        $node = new \PHPParser_Node_Stmt_Class('Glass', array(), array(
            'comments' => array(
                new \PHPParser_Comment('@mondrian contractor SomeNewContract')
            )
        ));

        $this->context->expects($this->once())
                ->method('pushNewContract')
                ->with('Glass', 'SomeNewContract');

        $this->visitor->enterNode($node);

        $this->assertTrue($this->visitor->isModified());
    }

    public function testDoNothingForCC()
    {
        $this->visitor->getGenerated();
        $node = new \PHPParser_Node_Stmt_Interface('Dummy');
        $stmt =  new \PHPParser_Node_Stmt_ClassMethod('dummy');

        $this->context
                ->expects($this->never())
                ->method('pushNewContract');
        $this->visitor->enterNode($node);
        $this->visitor->enterNode($stmt);
        $this->assertFalse($this->visitor->isModified());
    }

}