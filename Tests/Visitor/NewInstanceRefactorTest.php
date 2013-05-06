<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\NewInstanceRefactor;

/**
 * NewInstanceRefactorTest is a test for NewInstanceRefactor
 */
class NewInstanceRefactorTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;
    protected $dumper;

    protected function setUp()
    {
        $this->dumper = $this->getMockForAbstractClass('Trismegiste\Mondrian\Parser\PhpPersistence');
        $this->visitor = new NewInstanceRefactor($this->dumper);
    }

    public function testWithTraverser()
    {
        $classNode = new \PHPParser_Node_Stmt_Class('Victory', array(
            'stmts' => array(
                new \PHPParser_Node_Stmt_ClassMethod('holy', array(
                    'stmts' => array(
                        new \PHPParser_Node_Expr_New(new \PHPParser_Node_Name('Holy')),
                        new \PHPParser_Node_Expr_New(new \PHPParser_Node_Name('War'))
                    )
                        ))
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

        $pp = new \PHPParser_PrettyPrinter_Default();
        echo $pp->prettyPrint(array($classNode));
        $this->assertCount(3, $classNode->stmts);
    }

}