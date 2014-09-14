<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\SymbolMap\Collector;
use Trismegiste\Mondrian\Transform\ReflectionContext;
use Trismegiste\Mondrian\Parser\PackageParser;
use Symfony\Component\Finder\SplFileInfo;

/**
 * CollectorTest is a test for the visitor SymbolMap\Collector
 */
class CollectorTest extends \PHPUnit_Framework_TestCase
{

    protected $symbol = array();
    protected $visitor;
    protected $context;
    protected $parser;
    protected $traverser;

    public function setUp()
    {
        $this->context = new ReflectionContext();
        $mockGraphCtx = $this->getMockBuilder('Trismegiste\Mondrian\Transform\GraphContext')
                ->disableOriginalConstructor()
                ->getMock();
        $mockGraph = $this->getMock('Trismegiste\Mondrian\Graph\Graph');
        $this->visitor = new Collector($this->context, $mockGraphCtx, $mockGraph);
        $this->parser = new PackageParser(new \PHPParser_Parser(new \PHPParser_Lexer()));
        $this->traverser = new \PHPParser_NodeTraverser();
        $this->traverser->addVisitor($this->visitor);
    }

    public function testSimpleCase()
    {
        $iter = array(new SplFileInfo(__DIR__ . '/../../Fixtures/Project/Concrete.php', '/../../Fixtures/Project/', 'Concrete.php'));
        $stmts = $this->parser->parse(new \ArrayIterator($iter));
        $this->traverser->traverse($stmts);
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

}
