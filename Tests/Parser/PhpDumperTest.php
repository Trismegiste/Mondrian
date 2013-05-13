<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Parser;

use Trismegiste\Mondrian\Parser\PhpDumper;
use Trismegiste\Mondrian\Parser\PhpFile;

/**
 * PhpDumperTest tests the dumper
 */
class PhpDumperTest extends \PHPUnit_Framework_TestCase
{

    public function getNode()
    {
        return array(array(
                sys_get_temp_dir() . '/2del' . time() . '.php',
                new \PHPParser_Node_Stmt_Class('Trash')
        ));
    }

    /**
     * @dataProvider getNode
     */
    public function testWrite($dest, $node)
    {
        $dump = new PhpDumper();
        $dump->write(new PhpFile($dest, array($node)));
        $this->assertFileExists($dest);
    }

}
