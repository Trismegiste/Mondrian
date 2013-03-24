<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\SymbolMap;
use Trismegiste\Mondrian\Transform\Context;

/**
 * SymbolMapTest is a test for the visitor SymbolMap
 */
class SymbolMapTest extends \PHPUnit_Framework_TestCase
{

    protected $symbol = array();
    protected $visitor;
    protected $context;

    public function setUp()
    {
        $this->context = new Context($this->getMock('Trismegiste\Mondrian\Graph\Graph'));
        $this->visitor = new SymbolMap($this->context);
    }

    public function testExternalInterfaceInheritance()
    {
        $parser = new \PHPParser_Parser(new \PHPParser_Lexer());
        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor($this->visitor);

        $iter = array(__DIR__ . '/../Fixtures/Project/InheritExtra.php');
        foreach ($iter as $fch) {
            $code = file_get_contents($fch);
            $stmts = $parser->parse($code);
            $traverser->traverse($stmts);
        }

        $this->assertCount(2, $this->context->inheritanceMap);
    }

}