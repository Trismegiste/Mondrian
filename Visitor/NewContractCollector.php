<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Refactor\Refactored;

/**
 * NewContractCollector gather classe which needs to be refactor with a
 * contract
 */
class NewContractCollector extends PublicCollector
{

    protected $context;

    public function __construct(Refactored $ctx)
    {
        $this->context = $ctx;
    }

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

    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        
    }

    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        
    }

}