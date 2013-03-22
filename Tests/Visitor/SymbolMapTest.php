<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\SymbolMap;

/**
 * SymbolMapTest is a test for the visitor SymbolMap
 */
class SymbolMapTest extends \PHPUnit_Framework_TestCase
{

    protected $symbol = array();
    protected $visitor;

    public function setUp()
    {
        $this->visitor = new SymbolMap($this->symbol);
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
    }

}