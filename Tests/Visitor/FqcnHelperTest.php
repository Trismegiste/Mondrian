<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\FqcnHelper;

/**
 * FqcnHelperTest tests helper methods provided by FqcnHelper
 */
class FqcnHelperTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;
    protected $traverser;

    protected function setUp()
    {
        $this->visitor = new FqcnHelperStub();
        $this->traverser = new \PHPParser_NodeTraverser();
        $this->traverser->addVisitor($this->visitor);
    }

    /**
     * @expectedException PHPParser_Error
     */
    public function testDoubleAlias()
    {
        $node = array(
            new \PHPParser_Node_Stmt_UseUse(new \PHPParser_Node_Name('Simple\Aliasing'), 'ItFails'),
            new \PHPParser_Node_Stmt_UseUse(new \PHPParser_Node_Name('Double\Aliasing'), 'ItFails'),
        );
        $this->traverser->traverse($node);
    }

    public function testResolution()
    {
        $node[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Wrath\Of'));
        $node[1] = new \PHPParser_Node_Stmt_Class('TheNorsemen');
        $node[1]->extends = new \PHPParser_Node_Name('Khan');

        $this->traverser->traverse($node);
        $this->assertEquals('Wrath\Of\Khan', $node[1]->getAttribute('unit-test'));
    }

    public function testNoResolution()
    {
        $node[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Wrath\Of'));
        $node[1] = new \PHPParser_Node_Stmt_Class('TheNorsemen');
        $node[1]->extends = new \PHPParser_Node_Name_FullyQualified('Swansong\For\A\Raven');

        $this->traverser->traverse($node);
        $this->assertEquals('Swansong\For\A\Raven', $node[1]->getAttribute('unit-test'));
    }

    public function testResolutionWithAlias()
    {
        $node[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Wrath\Of'));
        $node[1] = new \PHPParser_Node_Stmt_UseUse(new \PHPParser_Node_Name('Medusa\And\Hemlock'), 'Nymphetamine');
        $node[2] = new \PHPParser_Node_Stmt_Class('TheNorsemen');
        $node[2]->extends = new \PHPParser_Node_Name('Nymphetamine');

        $this->traverser->traverse($node);
        $this->assertEquals('Medusa\And\Hemlock', $node[2]->getAttribute('unit-test'));
    }

    public function testNamespacedTransform()
    {
        $node[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Wrath\Of\The'));
        $node[1] = new \PHPParser_Node_Stmt_Interface('Norsemen');

        $this->traverser->traverse($node);
        $this->assertEquals('Wrath\Of\The\Norsemen', $node[1]->getAttribute('unit-test'));
    }

    public function testNamespacedTransformFallback()
    {
        $node[0] = new \PHPParser_Node_Stmt_Interface('Norsemen');

        $this->traverser->traverse($node);
        $this->assertEquals('Norsemen', $node[0]->getAttribute('unit-test'));
    }

    public function testResetAfterNewFile()
    {
        $this->visitor->enterNode(new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Nymphetamine')));
        $this->assertAttributeEquals('Nymphetamine', 'namespace', $this->visitor);
        $this->visitor->enterNode(new \Trismegiste\Mondrian\Parser\PhpFile('a', array()));
        $this->assertAttributeEquals(null, 'namespace', $this->visitor);
    }

    public function testReservedKeyword()
    {
        $node = new \PHPParser_Node_Expr_StaticCall(new \PHPParser_Node_Name('parent'), 'calling');
        $this->visitor->enterNode($node);
        $this->assertEquals('parent', $node->getAttribute('unit-test'));
    }

    public function testEnterFile()
    {
        $source = new \Trismegiste\Mondrian\Parser\PhpFile('a', array());
        $this->visitor->enterNode($source);
        $this->assertAttributeEquals($source, 'currentPhpFile', $this->visitor);
    }

}

/**
 * A subclass of FqcnHelper to test internal protected methods
 * (it's better than using ReflectionMethod, IMO )
 */
class FqcnHelperStub extends FqcnHelper
{

    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        switch ($node->getType()) {

            case 'Stmt_Class':
                if (!is_null($node->extends)) {
                    $node->setAttribute('unit-test', (string) $this->resolveClassName($node->extends));
                }
                break;

            case 'Stmt_Interface':
                if (!is_null($node->extends)) {
                    $node->setAttribute('unit-test', $this->getNamespacedName($node));
                }
                break;

            case 'Expr_StaticCall':
                $node->setAttribute('unit-test', (string) $this->resolveClassName($node->class));
                break;
        }
    }

}