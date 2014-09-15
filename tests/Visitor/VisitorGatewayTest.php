<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\VisitorGateway;

/**
 * VisitorGatewayTest tests the VisitorGateway
 */
class VisitorGatewayTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;
    protected $reflectionCtx;
    protected $graphCtx;
    protected $graph;

    private function buildVisitor(array $visitor = [])
    {
        $this->sut = new VisitorGateway($visitor, $this->reflectionCtx, $this->graphCtx, $this->graph);
    }

    protected function setUp()
    {
        $this->reflectionCtx = $this->getMock('Trismegiste\Mondrian\Transform\ReflectionContext');
        $this->graphCtx = $this->getMockBuilder('Trismegiste\Mondrian\Transform\GraphContext')
                ->disableOriginalConstructor()
                ->getMock();
        $this->graph = $this->getMock('Trismegiste\Mondrian\Graph\Graph');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmpty()
    {
        $this->buildVisitor();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadState()
    {
        $this->buildVisitor(['sfd']);
    }

    public function testStateKey()
    {
        $state0 = $this->getMock('Trismegiste\Mondrian\Visitor\State\State');
        $state0->expects($this->exactly(2))
                ->method('getName')
                ->will($this->returnValue('key0'));
        $state0->expects($this->once())
                ->method('setContext');

        $state1 = $this->getMock('Trismegiste\Mondrian\Visitor\State\State');
        $state1->expects($this->once())
                ->method('getName')
                ->will($this->returnValue('key1'));
        $state1->expects($this->once())
                ->method('setContext');

        $this->buildVisitor([$state0, $state1]);
    }

}