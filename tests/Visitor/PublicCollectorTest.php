<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\PublicCollector;

/**
 * PublicCollectorTest tests for PublicCollector visitor
 *
 * @author flo
 */
class PublicCollectorTest extends \PHPUnit\Framework\TestCase
{

    protected $visitor;

    protected function setUp():void
    {
        $this->visitor = $this->getMockForAbstractClass('Trismegiste\Mondrian\Visitor\PublicCollector');
    }

    public function testClassNodeWithoutNS()
    {
        $node = new \PHPParser_Node_Stmt_Class('Metal');
        $this->visitor->expects($this->once())
                ->method('enterClassNode')
                ->with($node);

        $this->visitor->enterNode($node);
        $this->assertAttributeEquals('Metal', 'currentClass', $this->visitor);
        $this->visitor->leaveNode($node);
        $this->assertAttributeEquals(false, 'currentClass', $this->visitor);
    }

    public function testPublicMethodNode()
    {
        $node = new \PHPParser_Node_Stmt_ClassMethod('fatigue');
        $this->visitor->expects($this->once())
                ->method('enterPublicMethodNode')
                ->with($node);

        $this->visitor->enterNode($node);
        $this->assertAttributeEquals('fatigue', 'currentMethod', $this->visitor);
        $this->visitor->leaveNode($node);
        $this->assertAttributeEquals(false, 'currentMethod', $this->visitor);
    }

    public function testNonPublicMethodNode()
    {
        $node = new \PHPParser_Node_Stmt_ClassMethod('fatigue');
        $node->type = \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED;
        $this->visitor->expects($this->never())
                ->method('enterPublicMethodNode');

        $this->visitor->enterNode($node);
        $this->assertAttributeEquals(false, 'currentMethod', $this->visitor);
        $this->visitor->leaveNode($node);
        $this->assertAttributeEquals(false, 'currentMethod', $this->visitor);
    }

    public function testInterfaceNodeWithoutNS()
    {
        $node = new \PHPParser_Node_Stmt_Interface('Home');
        $this->visitor->expects($this->once())
                ->method('enterInterfaceNode')
                ->with($node);

        $this->visitor->enterNode($node);
        $this->assertAttributeEquals('Home', 'currentClass', $this->visitor);
        $this->visitor->leaveNode($node);
        $this->assertAttributeEquals(false, 'currentClass', $this->visitor);
    }

    public function testTraitNodeWithoutNS()
    {
        $node = new \PHPParser_Node_Stmt_Trait('Popipo');
        $this->visitor->expects($this->once())
                ->method('enterTraitNode')
                ->with($node);

        $this->visitor->enterNode($node);
        $this->assertAttributeEquals('Popipo', 'currentClass', $this->visitor);
        $this->visitor->leaveNode($node);
        $this->assertAttributeEquals(false, 'currentClass', $this->visitor);
    }

}
