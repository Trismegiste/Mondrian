<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures;

use Symfony\Component\Finder\SplFileInfo;

/**
 * MockSplFileInfo is a fake SplFileInfo since PHP >= 5.4.29 decides
 * suddenly to not support unserialize with SplFileInfo.
 * 
 * https://bugs.php.net/bug.php?id=67072
 * https://github.com/padraic/mockery/issues/348
 */
class MockSplFileInfo extends SplFileInfo
{

    private $realPath;
    private $content;

    public function __construct($realPath, $content)
    {
        $this->realPath = $realPath;
        $this->content = $content;
    }

    public function getContents()
    {
        return $this->content;
    }

    public function getRealPath()
    {
        return $this->realPath;
    }

}