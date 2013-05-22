<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform;

use Trismegiste\Mondrian\Transform\GraphBuilder;
use Trismegiste\Mondrian\Builder\Compiler\Director;
use Trismegiste\Mondrian\Parser\PhpFile;

/**
 * GraphBuilderTest tests the builder compiler
 */
class GraphBuilderTest extends \PHPUnit_Framework_TestCase
{

    protected $builder;
    protected $director;
    protected $logger;
    protected $graph;

    protected function setUp()
    {
        $conf = array('calling' => array());
        $this->graph = $this->getMock('Trismegiste\Mondrian\Graph\Graph');
        $this->logger = $this->getMock('Trismegiste\Mondrian\Transform\Logger\LoggerInterface');
        $this->builder = new GraphBuilder($conf, $this->graph, $this->logger);
        $this->director = new Director($this->builder);
    }

    public function testParsing()
    {
        // $this->director->compile(array(new PhpFile('abc', array())));
        $fac = new \Trismegiste\Mondrian\Parser\BuilderFactory();
        $file = $fac->file('wesh.php')
                        ->ns('Project')
                        ->addStmt(
                                $fac->class('Hello')
                                ->implement('Kitty')
                                ->addStmt(
                                        $fac
                                        ->method('coucou')
                                        ->getNode()
                                )->getNode()
                        )->getNode();

        $pp = new \PHPParser_PrettyPrinter_Default();

        $stmts = iterator_to_array($file->getIterator());
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default();

        echo "<?php\n\n" . $prettyPrinter->prettyPrint($stmts);
    }

}