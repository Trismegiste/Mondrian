<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Refactor\Refactored;
use Trismegiste\Mondrian\Refactor\RefactorPass;

/**
 * InterfaceExtractor builds new contracts
 */
class InterfaceExtractor extends PublicCollector implements RefactorPass
{

    protected $newInterface = false;
    protected $newContent = null; // a list of interfaceName => content
    protected $methodStack;
    protected $context;

    public function __construct(Refactored $ctx)
    {
        $this->context = $ctx;
    }

    public function beforeTraverse(array $nodes)
    {
        $this->isDirty = false;
        $this->newContent = array();
    }

    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->extractAnnotation($node);
        if ($node->hasAttribute('contractor')) {
            $this->newInterface = reset($node->getAttribute('contractor'));
            $this->methodStack = array();
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        parent::leaveNode($node);

        if ($node->getType() === 'Stmt_Class') {
            $this->newContent[$this->newInterface] = $this->buildNewInterface();
            $this->newInterface = false;
        }
    }

    protected function buildNewInterface()
    {
        $fqcn = new \PHPParser_Node_Stmt_Namespace($this->newInterface);
        $interfaceShortcut = array_pop($fqcn->parts);
        $generated[0] = $fqcn;
        $generated[1] = new \PHPParser_Node_Stmt_Interface($interfaceShortcut, array('stmts' => $this->methodStack));

        return $generated;
    }

    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        
    }

    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        if (!preg_match('#^__.+#', $node->name) && $this->newInterface) {
            $this->enterStandardMethod($node);
        }
    }

    protected function enterStandardMethod(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $abstracted = clone $node;
        $abstracted->type = 0;
        $abstracted->stmts = null;

        $this->methodStack[] = $abstracted;
    }

    public function hasGenerated()
    {
        return count($this->newContent);
    }

    public function getGenerated()
    {
        return $this->newContent;
    }

}