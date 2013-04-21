<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\PublicCollector;

/**
 * PublicCollectorTest tests for PublicCollector visitor
 *
 * @author flo
 */
class PublicCollectorTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;

    protected function setUp()
    {
        $this->visitor = $this->getMockForAbstractClass('Trismegiste\Mondrian\Visitor\PublicCollector');
    }

    public function testClassNodeWithoutNS()
    {
        $node = new \PHPParser_Node_Stmt_Class('Metal');
        $this->visitor->enterNode($node);
        $this->assertAttributeEquals('Metal', 'currentClass', $this->visitor);
    }

}