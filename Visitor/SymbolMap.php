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
 * It fills the Context with symbols
 */
class SymbolMap extends \PHPParser_NodeVisitor_NameResolver implements CompilerPass
{

    protected $currentClass = false;
    private $context;

    public function __construct(Context $ctx)
    {
        $this->context = $ctx;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        switch ($node->getType()) {

            case 'Stmt_Class' :
                $this->currentClass = (string) $node->namespacedName;
                $this->context->initSymbol($this->currentClass, false);
                // extends
                if (!is_null($node->extends)) {
                    $name = (string) $node->extends;
                    $this->context->initSymbol($name, false);
                    $this->context->pushParentClass($this->currentClass, $name);
                }
                // implements
                foreach ($node->implements as $parent) {
                    $this->context->initSymbol((string) $parent, true);
                    $this->context->pushParentClass($this->currentClass, (string) $parent);
                }
                break;

            case 'Stmt_Interface' :
                $this->currentClass = (string) $node->namespacedName;
                $this->context->initSymbol($this->currentClass, true);
                // extends
                foreach ($node->extends as $interf) {
                    $this->context->initSymbol((string) $interf, true);
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
     * Compiling the pass : resolving symbol in the context
     */
    public function compile()
    {
        $this->context->resolveSymbol();
    }

}