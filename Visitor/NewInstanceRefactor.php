<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Parser\PhpPersistence;

/**
 * NewInstanceRefactor is a generator of method for each new instance
 */
class NewInstanceRefactor extends PublicCollector
{

    protected $currentMethodRelevant = false;
    protected $factoryMethodStack;
    protected $dumper;
    protected $currentClassStmts;

    public function __construct(PhpPersistence $callable)
    {
        $this->dumper = $callable;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        if (($node->getType() == 'Expr_New') && $this->currentMethodRelevant) {
            return $this->enterNewInstance($node);
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_ClassMethod':
                $this->currentMethodRelevant = false;
                break;

            case 'Stmt_Class':
                // generate
                if (count($this->factoryMethodStack) > 0) {
                    $this->currentPhpFile->modified();
                }
                foreach ($this->factoryMethodStack as $name => $calling) {
                    $factory = new \PHPParser_Node_Stmt_ClassMethod($name);
                    $factory->type = \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED;
                    $factory->params = $calling->args;
                    $class = $calling->getAttribute('classShortcut');
                    $factory->stmts = array(
                        new \PHPParser_Node_Stmt_Return(
                                new \PHPParser_Node_Expr_New(new \PHPParser_Node_Name($class), $factory->params)
                        )
                    );

                    $this->currentClassStmts[] = $factory;
                }
                break;
        }

        parent::leaveNode($node);
    }

    protected function enterNewInstance(\PHPParser_Node_Expr_New $node)
    {
        if ($node->class instanceof \PHPParser_Node_Name) {
            $classShortcut = (string) $node->class;
            $methodName = 'create' . $classShortcut . count($node->args);
            $calling = new \PHPParser_Node_Expr_MethodCall(new \PHPParser_Node_Expr_Variable('this'), $methodName);
            $calling->args = $node->args;
            $calling->setAttribute('classShortcut', $classShortcut);
            $this->factoryMethodStack[$methodName] = $calling;

            return $calling;
        }

        return null;
    }

    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->factoryMethodStack = array();
        // to prevent cloning in Traverser (workaround) :
        $this->currentClassStmts = &$node->stmts;
    }

    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        
    }

    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $this->currentMethodRelevant = count($node->stmts) > 1;
    }

    public function afterTraverse(array $nodes)
    {
        foreach ($nodes as $file) {
            if ($file->isModified()) {
                $this->dumper->write($file);
            }
        }
    }

}