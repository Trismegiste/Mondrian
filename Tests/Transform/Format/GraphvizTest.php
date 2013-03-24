<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Format;

use Trismegiste\Mondrian\Transform\Format\Graphviz;
use Trismegiste\Mondrian\Transform\Grapher;

/**
 * GraphvizTest is a test for Graphviz decorator
 */
class GraphvizTest extends \PHPUnit_Framework_TestCase
{

    protected $grapher;

    protected function setUp()
    {
        $this->grapher = new Grapher();
    }

    public function testGenerate()
    {
        $iter = array(
            __DIR__ . '/../../Fixtures/Project/NotConcreteTypedParam.php',
            __DIR__ . '/../../Fixtures/Project/ContractTypedParam.php',
            __DIR__ . '/../../Fixtures/Project/Contract.php'
        );
        $result = $this->grapher->parse($iter);
        $viz = new Graphviz($result);

        $this->assertStringStartsWith('digraph', $viz->export());
    }

}