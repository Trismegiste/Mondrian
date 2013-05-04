<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Parser;

/**
 * PhpDumper dumps a PhpFile into a file
 */
class PhpDumper
{

    protected $prettyPrinter;

    public function __construct()
    {
        $this->prettyPrinter = new \PHPParser_PrettyPrinter_Default();
    }

    /**
     * Write the file
     * 
     * @param \Trismegiste\Mondrian\Parser\PhpFile $aFile
     */
    public function write(PhpFile $aFile)
    {
        file_put_contents($aFile->getRealPath(), "<?php\n\n"
                . $this->prettyPrinter->prettyPrint(iterator_to_array($aFile->getIterator())));
    }

}