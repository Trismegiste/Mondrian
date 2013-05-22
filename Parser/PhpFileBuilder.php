<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Parser;

/**
 * PhpFileBuilder is a builder for a PhpFile node
 */
class PhpFileBuilder extends \PHPParser_BuilderAbstract
{

    protected $filename;
    protected $fileNamespace = false;
    protected $stack = array();

    public function __construct($absPath)
    {
        $this->filename = $absPath;
    }

    public function getNode()
    {
        return new PhpFile($this->fileNamespace, $this->stack);
    }

    public function addStmt($stmt)
    {
        $this->stack[] = $stmt;

        return $this;
    }

    public function ns($str)
    {
        array_unshift($this->stack, new \PHPParser_Node_Stmt_Namespace(
                        new \PHPParser_Node_Name((string) $str)));

        return $this;
    }

}