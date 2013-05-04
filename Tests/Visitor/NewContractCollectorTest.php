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

    protected function buildFileNode()
    {
        $node[] = new \Trismegiste\Mondrian\Parser\PhpFile('/I/Am/Victory.php', array());
        $node[] = new \PHPParser_Node_Stmt_Class('Victory');

        return $node;
    }

    public function testEnterClassWithoutComments()
    {
        $node = $this->buildFileNode();

        $this->context->expects($this->never())
                ->method('pushNewContract');

        foreach ($node as $item) {
            $this->visitor->enterNode($item);
        }
    }

    public function testEnterCommentedClassWithoutAnnotations()
    {
        $node = $this->buildFileNode();
        $node[1]->setAttribute('comments', array(
            new \PHPParser_Comment('Some useless comments')
        ));

        $this->context->expects($this->never())
                ->method('pushNewContract');

        foreach ($node as $item) {
            $this->visitor->enterNode($item);
        }
    }

    public function testEnterAnnotedClass()
    {
        $node = $this->buildFileNode();
        $node[1]->setAttribute('comments', array(
            new \PHPParser_Comment('@mondrian contractor SomeNewContract')
        ));

        $this->context->expects($this->once())
                ->method('pushNewContract')
                ->with('Victory', 'SomeNewContract');

        foreach ($node as $item) {
            $this->visitor->enterNode($item);
        }

        $this->assertTrue($node[0]->isModified());
    }

    public function testDoNothingForCC()
    {
        $node = new \PHPParser_Node_Stmt_Interface('Dummy');
        $stmt = new \PHPParser_Node_Stmt_ClassMethod('dummy');

        $this->context
                ->expects($this->never())
                ->method('pushNewContract');
        $this->visitor->enterNode($node);
        $this->visitor->enterNode($stmt);
    }

}