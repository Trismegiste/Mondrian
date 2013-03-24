<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\Context;

/**
 * SymbolMap is a class to collect list of class/interface/method name
 *
 */
class SymbolMap extends \PHPParser_NodeVisitor_NameResolver
{

    protected $symbol;
    protected $currentClass = false;

    public function __construct(Context $ctx)
    {
        $this->symbol = &$ctx->inheritanceMap;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        switch ($node->getType()) {

            case 'Stmt_Class' :
                $this->currentClass = (string) $node->namespacedName;
                $this->initSymbol($this->currentClass, false);
                // extends
                if (!is_null($node->extends)) {
                    $name = (string) $node->extends;
                    $this->initSymbol($name, false);
                    $this->symbol[$this->currentClass]['parent'][] = $name;
                }
                // implements
                foreach ($node->implements as $parent) {
                    $this->initSymbol((string) $parent, true);
                    $this->symbol[$this->currentClass]['parent'][] = (string) $parent;
                }
                break;

            case 'Stmt_Interface' :
                $this->currentClass = (string) $node->namespacedName;
                $this->initSymbol($this->currentClass, true);
                // extends
                foreach ($node->extends as $interf) {
                    $this->initSymbol((string) $interf, true);
                    $this->symbol[$this->currentClass]['parent'][] = (string) $interf;
                }
                break;

            case 'Stmt_ClassMethod' :
                $this->symbol[$this->currentClass]['method'][$node->name] = $this->currentClass;
                break;
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        switch ($node->getType()) {
            case 'Stmt_Class':
            case 'Stmt_Interface':
                $this->currentClass = false;
                break;
        }
    }

    protected function initSymbol($name, $isInterface)
    {
        if (!array_key_exists($name, $this->symbol)) {
            $this->symbol[$name]['interface'] = $isInterface;
            $this->symbol[$name]['parent'] = array();
            $this->symbol[$name]['method'] = array();
        }
    }

}