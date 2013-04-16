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
        $this->context = new Context();
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
        $this->visitor->compile();

        $this->assertAttributeEquals(array(
            'Project\\InheritExtra' => array(
                'interface' => false,
                'parent' => array(0 => 'IteratorAggregate'),
                'method' => array('getIterator' => 'IteratorAggregate')
            ),
            'IteratorAggregate' => array(
                'interface' => true,
                'parent' => array(),
                'method' => array()
            ),
                ), 'inheritanceMap', $this->context);
    }

    public function testSimpleCase()
    {
        $parser = new \PHPParser_Parser(new \PHPParser_Lexer());
        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor($this->visitor);

        $iter = array(__DIR__ . '/../Fixtures/Project/Concrete.php');
        foreach ($iter as $fch) {
            $code = file_get_contents($fch);
            $stmts = $parser->parse($code);
            $traverser->traverse($stmts);
        }
        $this->visitor->compile();

        $this->assertAttributeEquals(array(
            'Project\\Concrete' => array(
                'interface' => false,
                'parent' => array(),
                'method' => array('simple' => 'Project\\Concrete')
            ),
                ), 'inheritanceMap', $this->context);
    }

}