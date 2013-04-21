<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\PublicCollector;

/**
 * PublicCollectorStub is a stub for testing PublicCollector
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
        
    }

    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        
    }

    public function __construct(\PHPUnit_Framework_TestCase $track)
    {
        $this->testCase = $track;
    }

}