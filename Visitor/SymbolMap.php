<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\ReflectionContext;
use Trismegiste\Mondrian\Transform\CompilerPass;

/**
 * SymbolMap is a class to collect list of class/interface/method name
 * 
 * It fills the Context with symbols
 */
class SymbolMap extends PublicCollector implements CompilerPass
{

    private $context;

    /**
     * Build the collector
     * 
     * @param Context $ctx 
     */
    public function __construct(ReflectionContext $ctx)
    {
        $this->context = $ctx;
    }

    /**
     * {@inheritDoc}
     */
    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->context->initSymbol($this->currentClass, false);
        // extends
        if (!is_null($node->extends)) {
            $name = (string) $this->resolveClassName($node->extends);
            $this->context->initSymbol($name, false);
            $this->context->pushParentClass($this->currentClass, $name);
        }
        // implements
        foreach ($node->implements as $parent) {
            $name = (string) $this->resolveClassName($parent);
            $this->context->initSymbol($name, true);
            $this->context->pushParentClass($this->currentClass, $name);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        $this->context->initSymbol($this->currentClass, true);
        // extends
        foreach ($node->extends as $interf) {
            $name = (string) $this->resolveClassName($interf);
            $this->context->initSymbol($name, true);
            $this->context->pushParentClass($this->currentClass, $name);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $this->context->addMethodToClass($this->currentClass, $node->name);
    }

    /**
     * Compiling the pass : resolving symbols in the context
     */
    public function compile()
    {
        $this->context->resolveSymbol();
    }

}