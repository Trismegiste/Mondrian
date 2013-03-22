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
        $fqcnClass = 'Trismegiste\Mondrian\Tests\Fixtures\Project\NotConcreteTypedParam';
        $fqcnInterface = 'Trismegiste\Mondrian\Tests\Fixtures\Project\ContractTypedParam';
        $fqcnOtherInterface = 'Trismegiste\Mondrian\Tests\Fixtures\Project\Contract';
        $iter = array(
            __DIR__ . '/../Fixtures/Project/NotConcreteTypedParam.php',
            __DIR__ . '/../Fixtures/Project/ContractTypedParam.php',
            __DIR__ . '/../Fixtures/Project/Contract.php'
        );
        $result = $this->grapher->parse($iter);
        $viz = new Graphviz($result);

        $this->assertStringStartsWith('digraph', $viz->getDot());
    }

}