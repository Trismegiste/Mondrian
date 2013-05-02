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

}

/**
 * A subclass of FqcnHelper to test internal protected methods
 * (it's better than using ReflectionMethod, IMO )
 */
class FqcnHelperStub extends FqcnHelper
{

    private $testCase;

    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);
        if ($node->getType() == 'Stmt_Class') {
            if (!is_null($node->extends)) {
                $node->setAttribute('unit-test', (string) $this->resolveClassName($node->extends));
            }
        }
    }

}