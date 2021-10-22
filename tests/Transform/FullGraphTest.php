<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform;

use Trismegiste\Mondrian\Graph\Graph;
use Symfony\Component\Finder\Finder;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Builder\Linking;
use Trismegiste\Mondrian\Transform\GraphBuilder;
use Trismegiste\Mondrian\Builder\Statement\Builder;
use Trismegiste\Mondrian\Transform\Logger\NullLogger;

/**
 * FullGraphTest is functional tests for Grapher
 */
class FullGraphTest extends \PHPUnit\Framework\TestCase
{

    protected $graph;
    protected $compiler;

    protected function setUp():void
    {
        $conf = array('calling' => array());

        $this->graph = new Digraph();
        $this->compiler = new Linking(
                new Builder(), new GraphBuilder($conf, $this->graph, new NullLogger()));
    }

    protected function findVertex(Graph $g, $type, $name)
    {
        foreach ($g->getVertexSet() as $vertex) {
            if ((get_class($vertex) == $type) && ($vertex->getName() == $name)) {
                return $vertex;
            }
        }
        return null;
    }

    public function testSomeEdge()
    {
        $nsVertex = 'Trismegiste\Mondrian\Transform\Vertex\\';
        $scan = new Finder();
        $scan->files()->in(__DIR__ . '/../Fixtures/Project/');

        $this->compiler->run($scan->getIterator());
        $result = $this->graph;

        $classVertex = $this->findVertex($result, $nsVertex . 'ClassVertex', 'Project\Concrete');
        $this->assertNotNull($classVertex);

        $signature = $this->findVertex($result, $nsVertex . 'MethodVertex', 'Project\Concrete::simple');
        $this->assertNotNull($signature);

        $this->assertNotNull($result->searchEdge($classVertex, $signature));

        $impl = $this->findVertex($result, $nsVertex . 'ImplVertex', 'Project\Concrete::simple');
        $this->assertNotNull($impl);

        $this->assertNotNull($result->searchEdge($signature, $impl));
    }

}
