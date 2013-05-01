<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Parser;

use PHPParser_NodeAbstract;

/**
 * PhpFile is ...
 *
 * @author flo
 */
class PhpFile extends PHPParser_NodeAbstract
{

    protected $absPathName;

    public function __construct($path, array $stmts)
    {
        $this->absPathName = (string) $path;
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

}