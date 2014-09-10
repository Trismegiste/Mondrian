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

    public function __construct($path, array $stmts, $newFile = false)
    {
        $this->absPathName = (string) $path;
        parent::__construct($stmts, array('modified' => $newFile));
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
        return $this->getAttribute('modified');
    }

    public function modified()
    {
        $this->setAttribute('modified', true);
    }

}
