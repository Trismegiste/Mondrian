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

    protected $reflectionCtx;
    protected $graphCtx;
    protected $graph;

    /** @var VisitorGateway */
    protected $sut;

    private function buildVisitor(array $visitor = [])
    {
        $this->sut = new VisitorGateway($visitor, $this->reflectionCtx, $this->graphCtx, $this->graph);
    }

    private function buildVisitorUnique()
    {
        $state = $this->getMockState('key');
        $this->buildVisitor([$state]);
    }

    public function getMockState($key)
    {
        $state = $this->getMock('Trismegiste\Mondrian\Visitor\State\State');
        $state->expects($this->any())
                ->method('getName')
                ->will($this->returnValue($key));

        return $state;
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

    public function testEnteringNode()
    {
        $state = $this->getMockState('key');
        $state->expects($this->once())
                ->method('enter');
        $this->buildVisitor([$state]);
        $node = $this->getMock('PhpParser\Node');
        $this->sut->enterNode($node);
    }

    public function testGetState()
    {
        $this->buildVisitorUnique();
        $this->assertInstanceOf('Trismegiste\Mondrian\Visitor\State\State', $this->sut->getState('key'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUnknownState()
    {
        $this->buildVisitorUnique();
        $this->sut->getState('svfdgbtgf');
    }

    public function testShortcut()
    {
        $this->buildVisitorUnique();
        $this->sut->getGraph();
        $this->sut->getGraphContext();
        $this->sut->getReflectionContext();
    }

    public function testPushState()
    {
        $listing = [
            $this->getMockState('one'),
            $this->getMockState('two'),
            $this->getMockState('three')
        ];
        $this->buildVisitor($listing);
        $node = [
            $this->getMock('PhpParser\Node'),
            $this->getMock('PhpParser\Node'),
            $this->getMock('PhpParser\Node'),
        ];

        $this->assertNull($this->sut->getNodeFor('one'));

        $this->sut->pushState('two', $node[0]);
        $this->assertNull($this->sut->getNodeFor('one'));
        $this->assertEquals($node[0], $this->sut->getNodeFor('two'));

        $this->sut->pushState('three', $node[1]);
        $this->assertNull($this->sut->getNodeFor('one'));
        $this->assertEquals($node[0], $this->sut->getNodeFor('two'));
        $this->assertEquals($node[1], $this->sut->getNodeFor('three'));

        $this->sut->leaveNode($node[1]);
        $this->assertNull($this->sut->getNodeFor('one'));
        $this->assertEquals($node[0], $this->sut->getNodeFor('two'));
        $this->assertAttributeCount(2, 'stateStack', $this->sut);

        $this->sut->leaveNode($node[0]);
        $this->assertAttributeCount(1, 'stateStack', $this->sut);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoNodeFound()
    {
        $this->buildVisitorUnique();
        $this->sut->getNodeFor('fddbfgb');
    }

}