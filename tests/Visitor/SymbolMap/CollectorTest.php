<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\SymbolMap\Collector;
use Trismegiste\Mondrian\Transform\ReflectionContext;
use Trismegiste\Mondrian\Parser\PackageParser;
use Symfony\Component\Finder\SplFileInfo;
use Trismegiste\Mondrian\Tests\Fixtures\MockSplFileInfo;

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

    protected function scanFile($fixtures)
    {
        $iter = [];

        foreach ($fixtures as $fch) {
            $path = __DIR__ . '/../../Fixtures/Project/' . $fch;
            $code = file_get_contents($path);
            $iter[] = new MockSplFileInfo($path, $code);
        }

        $stmts = $this->parser->parse(new \ArrayIterator($iter));
        $this->traverser->traverse($stmts);
        $this->visitor->afterTraverse(array());
    }

    public function testSimpleCase()
    {
        $this->scanFile(['Concrete.php']);

        $this->assertAttributeEquals(array(
            'Project\\Concrete' => array(
                'type' => 'c',
                'parent' => array(),
                'method' => array('simple' => 'Project\\Concrete'),
                'use' => []
            ),
                ), 'inheritanceMap', $this->context);
    }

    public function testExternalInterfaceInheritance()
    {
        $this->scanFile(['InheritExtra.php']);

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

    public function testAliasing()
    {
        $this->scanFile(['Alias1.php', 'Alias2.php']);

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
        $this->scanFile(['SimpleTrait.php']);

        $this->assertAttributeEquals(array(
            'Project\\SimpleTrait' => array(
                'type' => 't',
                'parent' => [],
                'method' => ['someService' => 'Project\\SimpleTrait'],
                'use' => []
            )
                ), 'inheritanceMap', $this->context);
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

    public function testImportingMethodFromTraitWithInterfaceCollision()
    {
        $this->scanFile([
            'ServiceRight.php',
            'ServiceTrait.php',
            'ServiceInterface.php'
        ]);

        $this->assertAttributeEquals(array(
            'Project\\ServiceRight' => array(
                'type' => 'c',
                'parent' => ['Project\\ServiceInterface'],
                'method' => array('someService' => 'Project\\ServiceInterface'),
                'use' => ['Project\\ServiceTrait']
            ),
            'Project\\ServiceInterface' => array(
                'type' => 'i',
                'parent' => [],
                'method' => array('someService' => 'Project\\ServiceInterface'),
                'use' => []
            ),
            'Project\\ServiceTrait' => array(
                'type' => 't',
                'parent' => [],
                'method' => array('someService' => 'Project\\ServiceTrait'),
                'use' => []
            )), 'inheritanceMap', $this->context);
    }

}
