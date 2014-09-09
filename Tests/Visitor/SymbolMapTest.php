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
    protected $parser;
    protected $traverser;

    public function setUp()
    {
        $this->context = new ReflectionContext();
        $this->visitor = new SymbolMap($this->context);
        $this->parser = new \PHPParser_Parser(new \PHPParser_Lexer());
        $this->traverser = new \PHPParser_NodeTraverser();
        $this->traverser->addVisitor($this->visitor);
    }

    public function testExternalInterfaceInheritance()
    {
        $iter = array(__DIR__ . '/../Fixtures/Project/InheritExtra.php');
        foreach ($iter as $fch) {
            $code = file_get_contents($fch);
            $stmts = $this->parser->parse($code);
            $this->traverser->traverse($stmts);
        }
        $this->visitor->afterTraverse(array());

        $this->assertAttributeEquals(array(
            'Project\\InheritExtra' => array(
                'type' => 'c',
                'parent' => array(0 => 'IteratorAggregate'),
                'method' => array('getIterator' => 'IteratorAggregate'),
                'use' => []
            ),
            'IteratorAggregate' => array(
                'type' => 'i',
                'parent' => array(),
                'method' => array(),
                'use' => []
            ),
                ), 'inheritanceMap', $this->context);
    }

    public function testSimpleCase()
    {
        $iter = array(__DIR__ . '/../Fixtures/Project/Concrete.php');
        foreach ($iter as $fch) {
            $code = file_get_contents($fch);
            $stmts = $this->parser->parse($code);
            $this->traverser->traverse($stmts);
        }
        $this->visitor->afterTraverse(array());

        $this->assertAttributeEquals(array(
            'Project\\Concrete' => array(
                'type' => 'c',
                'parent' => array(),
                'method' => array('simple' => 'Project\\Concrete'),
                'use' => []
            ),
                ), 'inheritanceMap', $this->context);
    }

    public function testAliasing()
    {
        $iter = array(__DIR__ . '/../Fixtures/Project/Alias1.php',
            __DIR__ . '/../Fixtures/Project/Alias2.php');
        foreach ($iter as $fch) {
            $code = file_get_contents($fch);
            $stmts = $this->parser->parse($code);
            $this->traverser->traverse($stmts);
        }
        $this->visitor->afterTraverse(array());

        $this->assertAttributeEquals(array(
            'Project\\Aliasing' => array(
                'type' => 'c',
                'parent' => array('Project\Maid', 'Project\Peril'),
                'method' => array('spokes' => 'Project\\Aliasing'),
                'use' => []
            ),
            'Project\Maid' => array(
                'type' => 'c',
                'parent' => array(),
                'method' => array(),
                'use' => []
            ),
            'Project\Peril' => array(
                'type' => 'i',
                'parent' => array(),
                'method' => array(),
                'use' => []
            )
                ), 'inheritanceMap', $this->context);
    }

    public function testSimpleTrait()
    {
        $iter = array(__DIR__ . '/../Fixtures/Project/SimpleTrait.php');
        foreach ($iter as $fch) {
            $code = file_get_contents($fch);
            $stmts = $this->parser->parse($code);
            $this->traverser->traverse($stmts);
        }
        $this->visitor->afterTraverse(array());

        $this->assertAttributeEquals(array(
            'Project\\SimpleTrait' => array(
                'type' => 't',
                'parent' => [],
                'method' => ['someService' => 'Project\\SimpleTrait'],
                'use' => []
            )
                ), 'inheritanceMap', $this->context);
    }

    protected function scanFile($iter)
    {
        foreach ($iter as $fch) {
            $code = file_get_contents(__DIR__ . '/../Fixtures/Project/' . $fch);
            $stmts = $this->parser->parse($code);
            $this->traverser->traverse($stmts);
        }
        $this->visitor->afterTraverse(array());
    }

    public function testImportingMethodFromTrait()
    {
        $this->scanFile([
            'ServiceWrong.php',
            'ServiceTrait.php'
        ]);

        $this->assertAttributeEquals(array(
            'Project\\ServiceWrong' => array(
                'type' => 'c',
                'parent' => [],
                'method' => array('someService' => 'Project\\ServiceWrong'),
                'use' => ['Project\\ServiceTrait']
            ),
            'Project\\ServiceTrait' => array(
                'type' => 't',
                'parent' => [],
                'method' => array('someService' => 'Project\\ServiceTrait'),
                'use' => []
            )), 'inheritanceMap', $this->context);
    }

}
