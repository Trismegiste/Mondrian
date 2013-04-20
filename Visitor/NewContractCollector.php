<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

/**
 * NewContractCollector gather classe whcih needs to be refactor with a 
 * contract
 */
class NewContractCollector extends PublicCollector
{

    public $newContract = array();

    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->extractAnnotation($node);
        if ($node->hasAttribute('contractor')) {
            $futureContract = clone $node->namespacedName;
            $classShortcut = array_pop($futureContract->parts);
            $futureContract->parts[] = reset($node->getAttribute('contractor'));
            $this->newContract[(string) $node->namespacedName] = (string) $futureContract;
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        parent::leaveNode($node);

        if ($node->getType() === 'Stmt_Class') {
            $this->collectOn = false;
        }
    }

    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        
    }

    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        
    }

}