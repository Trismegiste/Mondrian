<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Mother;

/**
 * InterfaceExtractor is ...
 *
 * @author flo
 */
class InterfaceExtractor extends PublicCollector
{

    protected $collectOn = false;

    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->extractAnnotation($node);
        if ($node->hasAttribute('contractor')) {
            $this->collectOn = true;
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
        if (!preg_match('#^__.+#', $node->name)) {
            $this->enterStandardMethod($node);
        }
    }

    protected function enterStandardMethod(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $children = array();
        foreach ($node->getIterator() as $key => $child) {
            switch ($key) {
                case 'stmts':
                    $child = null;
                    break;
                case 'type':
                    $child = 0;
                    break;
            }
            $children[$key] = $child;
        }

        $this->contractNode[] = new \PHPParser_Node_Stmt_ClassMethod($node->name, $children, $node->getAttributes());
    }

}