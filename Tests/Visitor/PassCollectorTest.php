<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

/**
 * PassCollectorTest is a test for PassCollector
 */
class PassCollectorTest extends \PHPUnit_Framework_TestCase
{

    public function testCompile()
    {
        $pass = $this->getMockForAbstractClass(
                'Trismegiste\Mondrian\Visitor\PassCollector', array(
            $this->getMock('Trismegiste\Mondrian\Transform\ReflectionContext'),
            $this->getMock('Trismegiste\Mondrian\Transform\GraphContext'),
            $this->getMock('Trismegiste\Mondrian\Graph\Graph')
        ));

        $pass->compile();
    }

}