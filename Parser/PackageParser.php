<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Parser;

use Symfony\Component\Finder\SplFileInfo;
use PHPParser_Parser;

/**
 * PackageParser is a parser for multiple files
 */
class PackageParser
{

    protected $fileParser;

    public function __construct(PHPParser_Parser $parser)
    {
        $this->fileParser = $parser;
    }

    public function parse(\Iterator $iter)
    {
        $node = array();
        foreach ($iter as $fch) {
            $node[] = $this->createPhpFileNode($fch);
        }

        return $node;
    }

    protected function createPhpFileNode(SplFileInfo $fch)
    {
        return new PphFile($fch->getRealPath(), $this->fileParser->parse($fch->getContents()));
    }

}