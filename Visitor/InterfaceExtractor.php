<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Refactor\Refactored;
use Trismegiste\Mondrian\Parser\PhpPersistence;

/**
 * InterfaceExtractor builds new contracts
 */
class InterfaceExtractor extends PublicCollector
{

    protected $newInterface = false;
    protected $newContent = null; // a list of PhpFile
    protected $methodStack; // a temporary stack of methods for the currently new interface
    protected $context;
    protected $dumper;

    public function __construct(Refactored $ctx, PhpPersistence $callable)
    {
        $this->context = $ctx;
        $this->dumper = $callable;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeTraverse(array $nodes)
    {
        parent::beforeTraverse($nodes);
        $this->newContent = array();
    }

    /**
     * {@inheritDoc}
     */
    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->extractAnnotation($node);
        if ($node->hasAttribute('contractor')) {
            $this->newInterface = reset($node->getAttribute('contractor'));
            $this->methodStack = array();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function leaveNode(\PHPParser_Node $node)
    {
        if ($node->getType() === 'Stmt_Class') {
            if ($this->newInterface) {
                $this->newContent[] = $this->buildNewInterface();
            }
            $this->newInterface = false;
        }

        parent::leaveNode($node);
    }

    /**
     * Build the new PhpFile for the new contract
     * 
     * @return \Trismegiste\Mondrian\Parser\PhpFile
     * @throws \RuntimeException If no inside a PhpFile (WAT?)
     */
    protected function buildNewInterface()
    {
        if (!$this->currentPhpFile) {
            throw new \RuntimeException('Currently not in a PhpFile therefore no generation');
        }

        $fqcn = new \PHPParser_Node_Name_FullyQualified($this->currentClass);
        array_pop($fqcn->parts);
        $generated[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name($fqcn->parts));
        $generated[1] = new \PHPParser_Node_Stmt_Interface($this->newInterface, array('stmts' => $this->methodStack));

        $dst = dirname($this->currentPhpFile->getRealPath()) . '/' . $this->newInterface . '.php';

        return new \Trismegiste\Mondrian\Parser\PhpFile($dst, $generated, true);
    }

    /**
     * {@inheritDoc}
     */
    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        // I filter only good relevant methods (no __construct, __clone, __invoke ...)
        if (!preg_match('#^__.+#', $node->name) && $this->newInterface) {
            $this->enterStandardMethod($node);
        }
    }

    /**
     * Stacks the method for the new interface
     * 
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     */
    protected function enterStandardMethod(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $abstracted = clone $node;
        $abstracted->type = 0;
        $abstracted->stmts = null;

        $this->methodStack[] = $abstracted;
    }

    /**
     * {@inheritDoc}
     */
    public function afterTraverse(array $node)
    {
        $this->writeUpdated($node);
        $this->writeUpdated($this->newContent);
    }

    /**
     * Write a list of PhpFile
     * 
     * @param array $fileList
     */
    protected function writeUpdated(array $fileList)
    {
        foreach ($fileList as $file) {
            if ($file->isModified()) {
                $this->dumper->write($file);
            }
        }
    }

}