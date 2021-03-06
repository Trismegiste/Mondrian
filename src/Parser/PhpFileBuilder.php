<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Parser;

/**
 * PhpFileBuilder is a builder for a PhpFile node :
 * Enforces the PSR-0 : one class per file
 */
class PhpFileBuilder extends \PHPParser_BuilderAbstract
{

    protected $filename;
    protected $fileNamespace = false;
    protected $theClass = null;
    protected $useList = array();

    public function __construct($absPath)
    {
        $this->filename = $absPath;
    }

    public function getNode()
    {
        $stmts = array();
        if ($this->fileNamespace) {
            $stmts[] = $this->fileNamespace;
        }
        if (count($this->useList)) {
            $stmts = array_merge($stmts, $this->useList);
        }
        if (!is_null($this->theClass)) {
            $stmts[] = $this->theClass;
        }

        return new PhpFile($this->filename, $stmts);
    }

    /**
     * Declares a class or an interface
     *
     * @param mixed $stmt a Node_Stmt or a Node_Builder
     *
     * @return \Trismegiste\Mondrian\Parser\PhpFileBuilder this instance
     *
     * @throws \InvalidArgumentException
     */
    public function declaring($stmt)
    {
        $node = $this->normalizeNode($stmt);
        if (in_array($node->getType(), array('Stmt_Class', 'Stmt_Interface'))) {
            $this->theClass = $node;
        } else {
            throw new \InvalidArgumentException("Invalid node expected type " . $node->getType());
        }

        return $this;
    }

    /**
     * Namespace
     *
     * @param string $str
     *
     * @return \Trismegiste\Mondrian\Parser\PhpFileBuilder this instance
     */
    public function ns($str)
    {
        $this->fileNamespace = new \PHPParser_Node_Stmt_Namespace(
                        new \PHPParser_Node_Name((string) $str));

        return $this;
    }

    /**
     * Add an "use fqcn"
     *
     * @param string $str
     *
     * @return \Trismegiste\Mondrian\Parser\PhpFileBuilder this instance
     */
    public function addUse($str)
    {
        $this->useList[] = new \PHPParser_Node_Stmt_Use(
                        array(
                            new \PHPParser_Node_Stmt_UseUse(
                                    new \PHPParser_Node_Name(
                                            (string) $str))));

        return $this;
    }

}