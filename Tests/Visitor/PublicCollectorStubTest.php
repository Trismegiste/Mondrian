<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

/**
 * PublicCollectorStubTest tests for PublicCollectorStub visitor
 */
class PublicCollectorStubTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;
    protected $traverser;

    protected function setUp()
    {
        $this->visitor = new PublicCollectorStub($this);
        $this->traverser = new \PHPParser_NodeTraverser();
        $this->traverser->addVisitor($this->visitor);
    }

    public function testNamespacedClass()
    {
        $node = array(
            new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('The\Sixteen')),
            new \PHPParser_Node_Stmt_Class('MenOfTain')
        );

        $this->traverser->traverse($node);
    }

    public function testNamespacedInterface()
    {
        $node = array(
            new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Wardenclyffe')),
            new \PHPParser_Node_Stmt_Interface('Tower')
        );
        $node[1]->setAttribute('comments', array(new \PHPParser_Comment(' -noise- @mondrian Oneiric Moor  ')));

        $this->traverser->traverse($node);
    }

    public function testNamespacedClassMethod()
    {
        $node = array(
            new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('The\Sixteen')),
            new \PHPParser_Node_Stmt_Class('MenOfTain')
        );
        $node[1]->stmts = array(new \PHPParser_Node_Stmt_ClassMethod('eidolon'));

        $this->traverser->traverse($node);
    }

    public function testNamespacedTrait()
    {
        $node = array(
            new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('All\Our')),
            new \PHPParser_Node_Stmt_Trait('Yesterdays')
        );

        $this->traverser->traverse($node);
    }

}
