<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

/**
 * RefactorTemplate is an abstract full functional test
 * for refactor service
 */
abstract class RefactorTemplate extends \PHPUnit\Framework\TestCase
{

    protected $coder;
    protected $dumper;

    protected function setUp():void
    {
        $this->dumper = new VirtualPhpDumper($this, __DIR__ . '/../Fixtures/Refact/');
    }

    protected function verifyMockObjects()
    {
        parent::verifyMockObjects();
        $this->dumper->verifyCalls();
    }

}
