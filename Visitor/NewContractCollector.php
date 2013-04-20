<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Refactor\Refactored;

/**
 * NewContractCollector gather classe whcih needs to be refactor with a 
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
            $futureContract = clone $node->namespacedName;
            $classShortcut = array_pop($futureContract->parts);
            $futureContract->parts[] = reset($node->getAttribute('contractor'));
            $this->context->newContract[(string) $node->namespacedName] = (string) $futureContract;
        }
    }

    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        
    }

    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        
    }

}