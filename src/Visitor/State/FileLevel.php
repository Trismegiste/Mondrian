<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;
use Trismegiste\Mondrian\Transform\ReflectionContext;

/**
 * FileLevel is ...
 */
class FileLevel extends AbstractState
{

    /**
     * @var null|PHPParser_Node_Name Current namespace
     */
    protected $namespace;

    /**
     * @var array Currently defined namespace and class aliases
     */
    protected $aliases;

    public function enter(Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_Namespace' :
                $this->namespace = $node->name;
                $this->aliases = array();
                break;

            case 'Stmt_UseUse' :
                if (isset($this->aliases[$node->alias])) {
                    throw new \PhpParser\Error(
                    sprintf(
                            'Cannot use "%s" as "%s" because the name is already in use', $node->name, $node->alias
                    ), $node->getLine()
                    );
                }
                $this->aliases[$node->alias] = $node->name;
                break;

            case 'Stmt_Class':
                $this->context->pushState('class', $node);
                $this->enterClassNode($node);
                break;

            case 'Stmt_Trait':
                $this->context->pushState('trait', $node);
                $this->enterTraitNode($node);
                break;

            case 'Stmt_Interface':
                $this->context->pushState('interface', $node);
                $this->enterInterfaceNode($node);
                break;
        }
    }

    protected function enterClassNode(Node\Stmt\Class_ $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $this->context->getReflectionContext()->initSymbol($fqcn, ReflectionContext::SYMBOL_CLASS);
        // extends
        if (!is_null($node->extends)) {
            $name = (string) $this->resolveClassName($node->extends);
            $this->context->getReflectionContext()->initSymbol($name, ReflectionContext::SYMBOL_CLASS);
            $this->context->getReflectionContext()->pushParentClass($fqcn, $name);
        }
        // implements
        foreach ($node->implements as $parent) {
            $name = (string) $this->resolveClassName($parent);
            $this->context->getReflectionContext()->initSymbol($name, ReflectionContext::SYMBOL_INTERFACE);
            $this->context->getReflectionContext()->pushParentClass($fqcn, $name);
        }
    }

    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $this->context->getReflectionContext()->initSymbol($fqcn, ReflectionContext::SYMBOL_INTERFACE);
        // extends
        foreach ($node->extends as $interf) {
            $name = (string) $this->resolveClassName($interf);
            $this->context->getReflectionContext()->initSymbol($name, ReflectionContext::SYMBOL_INTERFACE);
            $this->context->getReflectionContext()->pushParentClass($fqcn, $name);
        }
    }

    protected function enterTraitNode(\PHPParser_Node_Stmt_Trait $node)
    {
        $fqcn = $this->getNamespacedName($node);
        $this->context->getReflectionContext()->initSymbol($fqcn, ReflectionContext::SYMBOL_TRAIT);
    }

    public function getName()
    {
        return 'file';
    }

    /**
     * resolve the Name with current namespace and alias
     *
     * @param \PHPParser_Node_Name $src
     * @return \PHPParser_Node_Name|\PHPParser_Node_Name_FullyQualified
     */
    public function resolveClassName(Node\Name $src)
    {
        $name = clone $src;
        // don't resolve special class names
        if (in_array((string) $name, array('self', 'parent', 'static'))) {
            return $name;
        }

        // fully qualified names are already resolved
        if ($name->isFullyQualified()) {
            return $name;
        }

        // resolve aliases (for non-relative names)
        if (!$name->isRelative() && isset($this->aliases[$name->getFirst()])) {
            $name->setFirst($this->aliases[$name->getFirst()]);
            // if no alias exists prepend current namespace
        } elseif (null !== $this->namespace) {
            $name->prepend($this->namespace);
        }

        return new Node\Name\FullyQualified($name->parts, $name->getAttributes());
    }

    /**
     * Helper : get the FQCN of the given $node->name
     *
     * @param PHPParser_Node $node
     * @return string
     */
    public function getNamespacedName(Node $node)
    {
        if (null !== $this->namespace) {
            $namespacedName = clone $this->namespace;
            $namespacedName->append($node->name);
        } else {
            $namespacedName = $node->name;
        }

        return (string) $namespacedName;
    }

}