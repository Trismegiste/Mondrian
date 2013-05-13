<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\SymbolMap;
use Trismegiste\Mondrian\Transform\ReflectionContext;

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
        $this->context = new ReflectionContext();
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
        $this->visitor->afterTraverse(array());

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
        $this->visitor->afterTraverse(array());

        $this->assertAttributeEquals(array(
            'Project\\Concrete' => array(
                'interface' => false,
                'parent' => array(),
                'method' => array('simple' => 'Project\\Concrete')
            ),
                ), 'inheritanceMap', $this->context);
    }

    public function testAliasing()
    {
        $parser = new \PHPParser_Parser(new \PHPParser_Lexer());
        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor($this->visitor);

        $iter = array(__DIR__ . '/../Fixtures/Project/Alias1.php',
            __DIR__ . '/../Fixtures/Project/Alias2.php');
        foreach ($iter as $fch) {
            $code = file_get_contents($fch);
            $stmts = $parser->parse($code);
            $traverser->traverse($stmts);
        }
        $this->visitor->afterTraverse(array());

        $this->assertAttributeEquals(array(
            'Project\\Aliasing' => array(
                'interface' => false,
                'parent' => array('Project\Maid', 'Project\Peril'),
                'method' => array('spokes' => 'Project\\Aliasing')
            ),
            'Project\Maid' => array(
                'interface' => false,
                'parent' => array(),
                'method' => array()
            ),
            'Project\Peril' => array(
                'interface' => true,
                'parent' => array(),
                'method' => array()
            )
                ), 'inheritanceMap', $this->context);
    }

}
