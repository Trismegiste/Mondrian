<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Refactor\Refactored;
use Trismegiste\Mondrian\Refactor\RefactorPass;

/**
 * NewContractCollector gather classe which needs to be refactor with a 
 * contract
 */
class NewContractCollector extends PublicCollector implements RefactorPass
{

    protected $context;
    protected $isDirty = false;

    public function __construct(Refactored $ctx)
    {
        $this->context = $ctx;
    }

    public function beforeTraverse(array $nodes)
    {
        $this->isDirty = false;
    }

    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->extractAnnotation($node);
        if ($node->hasAttribute('contractor')) {
            $interfaceName = new \PHPParser_Node_Name(reset($node->getAttribute('contractor')));
            $this->context->newContract[$this->getNamespacedName($node)] = (string) $this->resolveClassName($interfaceName);
            $node->implements[] = $interfaceName;
            $this->isDirty = true;
        }
    }

    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        
    }

    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        
    }

    public function isModified()
    {
        return $this->isDirty;
    }

    public function hasGenerated()
    {
        return false;
    }

    public function getGenerated()
    {
        
    }

}