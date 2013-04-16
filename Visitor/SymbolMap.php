<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\Context;
use Trismegiste\Mondrian\Transform\CompilerPass;

/**
 * SymbolMap is a class to collect list of class/interface/method name
 * 
 * It fills the inheritance map of the context
 * @todo Need refactor for SRP : some methods are bound to Context not this visitor
 */
class SymbolMap extends \PHPParser_NodeVisitor_NameResolver implements CompilerPass
{

    protected $symbol; // @todo replace all sets to this array by method calls of Context
    protected $currentClass = false;
    protected $context;

    public function __construct(Context $ctx)
    {
        $this->context = $ctx;
        $this->symbol = &$ctx->inheritanceMap; // @todo Demeter's law is patently broken
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
                    $this->context->pushParentClass($this->currentClass, $name);
                }
                // implements
                foreach ($node->implements as $parent) {
                    $this->initSymbol((string) $parent, true);
                    $this->context->pushParentClass($this->currentClass, (string) $parent);
                }
                break;

            case 'Stmt_Interface' :
                $this->currentClass = (string) $node->namespacedName;
                $this->initSymbol($this->currentClass, true);
                // extends
                foreach ($node->extends as $interf) {
                    $this->initSymbol((string) $interf, true);
                    $this->context->pushParentClass($this->currentClass, (string) $interf);
                }
                break;

            case 'Stmt_ClassMethod' :
                if ($node->isPublic()) {
                    $this->context->addMethodToClass($this->currentClass, $node->name);
                }
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

    /**
     * Initialize a new symbol
     * 
     * @param string $name class or interface name
     * @param bool $isInterface is interface ?
     */
    protected function initSymbol($name, $isInterface)
    {
        $this->context->initSymbol($name, $isInterface);
    }

    /**
     * Compiling the pass : resolving symbol in the context
     */
    public function compile()
    {
        $this->context->resolveSymbol();
    }

}