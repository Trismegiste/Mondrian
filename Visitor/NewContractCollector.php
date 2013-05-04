<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Refactor\Refactored;

/**
 * NewContractCollector gather classe which needs to be refactor with a contract. 
 * 
 * Adds the new interface so changes could be made to the current PhpFile
 */
class NewContractCollector extends PublicCollector
{

    protected $context;

    public function __construct(Refactored $ctx)
    {
        $this->context = $ctx;
    }

    /**
     * {@inheritDoc}
     */
    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->extractAnnotation($node);
        if ($node->hasAttribute('contractor')) {
            $interfaceName = new \PHPParser_Node_Name(reset($node->getAttribute('contractor')));
            $this->context->pushNewContract($this->getNamespacedName($node), (string) $this->resolveClassName($interfaceName));
            $node->implements[] = $interfaceName;
            $this->currentPhpFile->modified();
        }
    }

    /**
     * do nothing
     */
    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        
    }

    /**
     * do nothing
     */
    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        
    }

}