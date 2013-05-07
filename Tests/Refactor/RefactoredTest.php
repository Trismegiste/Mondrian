<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Refactor\Refactored;

/**
 * RefactoredTest tests for refactoring context
 */
class RefactoredTest extends \PHPUnit_Framework_TestCase
{

    protected $content;

    protected function setUp()
    {
        $this->content = new Refactored();
        $this->content->pushNewContract('Glass', 'Prison');
    }

    public function testPushNewContract()
    {
        $this->assertAttributeEquals(array('Glass' => 'Prison'), 'newContract', $this->content);
    }

    public function testHasNewContract()
    {
        $this->assertTrue($this->content->hasNewContract('Glass'));
    }

    public function testGetNewContract()
    {
        $this->assertEquals('Prison', $this->content->getNewContract('Glass'));
    }

}