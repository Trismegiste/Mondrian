<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Parser;

use PHPParser_NodeAbstract;

/**
 * PhpFile is a node in a package repreenting a file
 *
 */
class PhpFile extends PHPParser_NodeAbstract
{

    protected $absPathName;
    protected $modified;

    public function __construct($path, array $stmts, $newFile = false)
    {
        $this->absPathName = (string) $path;
        $this->modified = $newFile;
        parent::__construct($stmts);
    }

    public function getType()
    {
        return 'PhpFile';
    }

    public function getRealPath()
    {
        return $this->absPathName;
    }

    public function isModified()
    {
        return $this->modified;
    }

    public function modified()
    {
        $this->modified = true;
    }

}