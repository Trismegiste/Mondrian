<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

/**
 * RefactorTemplate is an abstract full functional test 
 * for refactor service
 */
abstract class RefactorTemplate extends \PHPUnit_Framework_TestCase
{

    protected $coder;
    protected $dumper;

    protected function setUp()
    {
        $this->dumper = new VirtualPhpDumper($this, __DIR__ . '/../Fixtures/Refact/');
    }

    protected function verifyMockObjects()
    {
        parent::verifyMockObjects();
        $this->dumper->verifyCalls();
    }

}