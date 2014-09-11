<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Builder\Statement;

use Trismegiste\Mondrian\Builder\Statement\Builder;

/**
 * BuilderTest tests the build of the parser
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{

    protected $parser;

    protected function getMockFile($absolute, $content)
    {
        return new \Trismegiste\Mondrian\Tests\Fixtures\MockSplFileInfo(($absolute), $content);
    }

    protected function setUp()
    {
        $this->parser = new Builder();
        $this->parser->buildLexer();
        $this->parser->buildFileLevel();
        $this->parser->buildPackageLevel();
    }

    public function testParsing()
    {
        $iter = new \ArrayIterator(array($this->getMockFile('abc', '<?php class abc {}')));
        $stmt = $this->parser->getParsed($iter);
        $this->assertCount(1, $stmt);
        $this->assertInstanceOf('Trismegiste\Mondrian\Parser\PhpFile', $stmt[0]);
        $content = $stmt[0]->getIterator();
        $this->assertCount(1, $content);
        $content->rewind();
        $this->assertEquals('Stmt_Class', $content->current()->getType());
        $this->assertEquals('abc', $content->current()->name);
    }

}