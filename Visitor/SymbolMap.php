<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

/**
 * SymbolMap is a class to collect list of class/interface/method name
 *
 */
class SymbolMap extends \PHPParser_NodeVisitor_NameResolver
{

    protected $symbol = array();
    protected $currentClass = false;

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

    public function afterTraverse(array $nodes)
    {
        foreach ($this->symbol as $className => $info) {
            $method = $info['method'];
            foreach ($method as $methodName => $declaringClass) {
                $upper = $this->recursivDeclaration($declaringClass, $methodName);
                if (!is_null($upper)) {
                    $this->symbol[$className]['method'][$methodName] = $upper;
                }
            }
        }

        print_r($this->symbol);
    }

    private function recursivDeclaration($current, $m)
    {
        $higher = null;

        if (array_key_exists($m, $this->symbol[$current]['method'])) {
            // default declarer :
            $higher = $this->symbol[$current]['method'][$m];
        } elseif (interface_exists($current) || class_exists($current)) {
            if (method_exists($current, $m)) {
                $higher = $current;
            }
        }

        // higher parent ?
        foreach ($this->symbol[$current]['parent'] as $mother) {
            $tmp = $this->recursivDeclaration($mother, $m);
            if (!is_null($tmp)) {
                $higher = $tmp;
                break;
            }
        }

        return $higher;
    }

}