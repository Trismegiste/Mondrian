<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\PublicCollector;

/**
 * PublicCollectorStub is a stub for inner testing PublicCollector
 */
class PublicCollectorStub extends PublicCollector
{

    private $testCase;

    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->testCase->assertEquals('The\Sixteen\MenOfTain', $this->currentClass);
    }

    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        $this->testCase->assertEquals('Wardenclyffe\Tower', $this->currentClass);
        $this->extractAnnotation($node);
        $this->testCase->assertEquals(array('Moor'), $node->getAttribute('Oneiric'));
    }

    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $this->testCase->assertEquals('eidolon', $this->currentMethod);
        $this->testCase->assertEquals('The\Sixteen\MenOfTain::eidolon', $this->getCurrentMethodIndex());
    }

    public function __construct(\PHPUnit_Framework_TestCase $track)
    {
        $this->testCase = $track;
    }

    protected function enterTraitNode(\PHPParser_Node_Stmt_Trait $node)
    {
        $this->testCase->assertEquals('All\Our\Yesterdays', $this->currentClass);
    }

}
