<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\ParamRefactor;
use Trismegiste\Mondrian\Parser\PhpFile;

/**
 * ParamRefactorTest is a test for ParamRefactor
 */
class ParamRefactorTest extends \PHPUnit\Framework\TestCase
{

    protected $visitor;
    protected $context;

    protected function setUp():void
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
        $fileNode = new PhpFile('/I/Am/Victory.php', array());
        $classNode = new \PHPParser_Node_Param('obj', null, new \PHPParser_Node_Name('SplObjectStorage'));

        $this->context->expects($this->once())
                ->method('hasNewContract')
                ->with('SplObjectStorage')
                ->will($this->returnValue(true));

        $this->visitor->enterNode($fileNode);
        $this->visitor->enterNode($classNode);
        $this->assertTrue($fileNode->isModified());
    }

    public function testClassTypedParamWithFqcn()
    {
        $fileNode = new PhpFile('/I/Am/Victory.php', array());
        $node = new \PHPParser_Node_Param('obj', null, new \PHPParser_Node_Name_FullyQualified('Pull\Me\Under'));

        $this->context->expects($this->once())
                ->method('hasNewContract')
                ->with('Pull\Me\Under')
                ->will($this->returnValue(true));

        $this->visitor->enterNode($fileNode);
        $this->visitor->enterNode($node);
        $this->assertTrue($fileNode->isModified());
    }

    public function testRefactoring()
    {
        $fileNode = new PhpFile('/I/Am/Victory.php', array());
        $node = new \PHPParser_Node_Param('obj', null, new \PHPParser_Node_Name_FullyQualified('Pull\Me\Under'));

        $this->context->expects($this->once())
                ->method('hasNewContract')
                ->with('Pull\Me\Under')
                ->will($this->returnValue(true));

        $this->context->expects($this->once())
                ->method('getNewContract')
                ->with('Pull\Me\Under')
                ->will($this->returnValue('Awake'));

        $this->visitor->enterNode($fileNode);
        $this->visitor->enterNode($node);
        $this->assertTrue($fileNode->isModified());
        $this->assertEquals('Awake', $node->type, 'Type Hint changed');
    }

    public function testWithTraverser()
    {
        $this->context->expects($this->once())
                ->method('hasNewContract')
                ->with('Pull\Me\Under')
                ->will($this->returnValue(true));

        $this->context->expects($this->once())
                ->method('getNewContract')
                ->with('Pull\Me\Under')
                ->will($this->returnValue('Awake'));

        $classNode = new \PHPParser_Node_Stmt_Class('Victory', array(
                    'stmts' => array(
                        new \PHPParser_Node_Stmt_ClassMethod('holy', array(
                            'params' => array(new \PHPParser_Node_Param('war', null, new \PHPParser_Node_Name('Pull\Me\Under')))
                        ))
                        )), array(
                    'comments' => array(
                        new \PHPParser_Comment('@mondrian contractor SomeNewContract')
                    )
                ));
        $file = new \Trismegiste\Mondrian\Parser\PhpFile('/I/Am/Victory.php', array(
                    $classNode
                ));

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor($this->visitor);

        $this->assertFalse($file->isModified());
        $traverser->traverse(array($file));
        $this->assertTrue($file->isModified());
        $this->assertEquals('Awake', (string) $classNode->stmts[0]->params[0]->type);
    }

}
