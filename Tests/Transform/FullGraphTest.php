<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform;

use Trismegiste\Mondrian\Transform\Grapher;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * FullGraphTest tests for Grapher
 */
class FullGraphTest extends \PHPUnit_Framework_TestCase
{

    protected $grapher;

    protected function setUp()
    {
        $this->grapher = new Grapher();
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
        $iter = new \DirectoryIterator(__DIR__ . '/../Fixtures/Project/');
        $batch = array();
        foreach ($iter as $fch) {
            if ($iter->isFile()) {
                $batch[] = $fch->getRealPath();
            }
        }
        $result = $this->grapher->parse($batch);

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