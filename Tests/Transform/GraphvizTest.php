<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform;

use Trismegiste\Mondrian\Transform\Graphviz;
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
        $fqcnClass = 'Trismegiste\Mondrian\Tests\Fixtures\Graph\NotConcreteTypedParam';
        $fqcnInterface = 'Trismegiste\Mondrian\Tests\Fixtures\Graph\ContractTypedParam';
        $fqcnOtherInterface = 'Trismegiste\Mondrian\Tests\Fixtures\Graph\Contract';
        $iter = array(
            __DIR__ . '/../Fixtures/Graph/NotConcreteTypedParam.php',
            __DIR__ . '/../Fixtures/Graph/ContractTypedParam.php',
            __DIR__ . '/../Fixtures/Graph/Contract.php'
        );
        $result = $this->grapher->parse($iter);
        $viz = new Graphviz($result);

        $this->assertStringStartsWith('digraph', $viz->getDot());
    }

}