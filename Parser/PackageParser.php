<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Parser;

use Symfony\Component\Finder\SplFileInfo;
use PHPParser_Parser;
use PHPParser_Lexer;

/**
 * PackageParser is a parser for multiple files
 */
class PackageParser
{

    public function parse(\Iterator $iter)
    {
        $parser = new PHPParser_Parser(new PHPParser_Lexer());

        $node = array();
        foreach ($iter as $fch) {
            $node[] = $this->createPhpFileNode($parser, $fch);
        }

        return $node;
    }

    protected function createPhpFileNode(PHPParser_Parser $parser, SplFileInfo $fch)
    {
        return new PphFile($fch->getRealPath(), $parser->parse($fch->getContents()));
    }

}