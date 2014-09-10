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

    /**
     * The ctor needs a service for persistence of modified files
     *
     * @param \Trismegiste\Mondrian\Parser\PhpPersistence $callable
     */
    public function __construct(PhpPersistence $callable)
    {
        $this->dumper = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        if (($node->getType() == 'Expr_New') && $this->currentMethodRelevant) {
            return $this->enterNewInstance($node);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(\PHPParser_Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_ClassMethod':
                $this->currentMethodRelevant = false;
                break;

            case 'Stmt_Class':
                // generate
                foreach ($this->factoryMethodStack as $name => $calling) {
                    $factory = new \PHPParser_Node_Stmt_ClassMethod($name);
                    $factory->type = \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED;
                    $factory->params = $this->getProcessedArgument($calling->args);
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

        return parent::leaveNode($node);
    }

    private function getProcessedArgument(array $args)
    {
        $param = array();
        foreach ($args as $idx => $argument) {
            if ($argument->value->getType() === 'Expr_Variable') {
                $paramName = $argument->value->name;
            } else {
                $paramName = 'param' . $idx;
            }
            $newParam = new \PHPParser_Node_Param($paramName);
            $param[$idx] = $newParam;
        }

        return $param;
    }

    /**
     * Enter in a new instance statement (only process "hard-coded" classname)
     *
     * @param \PHPParser_Node_Expr_New $node
     * @return \PHPParser_Node_Expr_MethodCall|null
     */
    protected function enterNewInstance(\PHPParser_Node_Expr_New $node)
    {
        if ($node->class instanceof \PHPParser_Node_Name) {
            $classShortcut = (string) $node->class;
            $methodName = 'create' . str_replace('\\', '_', $classShortcut) . count($node->args);
            $calling = new \PHPParser_Node_Expr_MethodCall(new \PHPParser_Node_Expr_Variable('this'), $methodName);
            $calling->args = $node->args;
            $calling->setAttribute('classShortcut', $classShortcut);
            $this->factoryMethodStack[$methodName] = $calling;
            $this->currentPhpFile->modified();

            return $calling;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->factoryMethodStack = array();
        // to prevent cloning in Traverser (workaround) :
        $this->currentClassStmts = &$node->stmts;
    }

    /**
     * {@inheritdoc}
     */
    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        // only refactor a method if it contains more than 1 statements (would be pointless otherwise, IMO)
        $this->currentMethodRelevant = count($node->stmts) > 1;
    }

    /**
     * Writes modified files
     *
     * @param array $nodes
     */
    public function afterTraverse(array $nodes)
    {
        foreach ($nodes as $file) {
            if ($file->isModified()) {
                $this->dumper->write($file);
            }
        }
    }

    protected function enterTraitNode(\PHPParser_Node_Stmt_Trait $node)
    {
        // @todo creating a new protected factory for a trait makes sense
    }

}

