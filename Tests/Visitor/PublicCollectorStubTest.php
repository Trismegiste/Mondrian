<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\PublicCollector;

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

    public function testNamespaceDecoration()
    {
        $node = array(
            new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('The\Sixteen')),
            new \PHPParser_Node_Stmt_Class('MenOfTain')
        );

        $this->traverser->traverse($node);
    }

}