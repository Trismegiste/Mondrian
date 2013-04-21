<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\FqcnHelper;

/**
 * FqcnHelperTest is ...
 *
 * @author flo
 */
class FqcnHelperTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;
    protected $traverser;

    protected function setUp()
    {
        $this->visitor = new FqcnHelper();
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

}