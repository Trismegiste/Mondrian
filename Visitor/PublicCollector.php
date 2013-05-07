<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

/**
 * PublicCollector is an abstract node collector for public "things" of types
 *
 */
abstract class PublicCollector extends FqcnHelper
{

    protected $currentClass = false;
    protected $currentMethod = false;

    /**
     * Visits a class node
     *
     * @param \PHPParser_Node_Stmt_Class $node
     */
    abstract protected function enterClassNode(\PHPParser_Node_Stmt_Class $node);

    /**
     * Visits an interface node
     *
     * @param \PHPParser_Node_Stmt_Interface $node
     */
    abstract protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node);

    /**
     * Visits a public method node
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     */
    abstract protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node);

    /**
     * {@inheritDoc}
     */
    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        switch ($node->getType()) {

            case 'Stmt_Class' :
                $this->currentClass = $this->getNamespacedName($node);
                $this->enterClassNode($node);
                break;

            case 'Stmt_Interface' :
                $this->currentClass = $this->getNamespacedName($node);
                $this->enterInterfaceNode($node);
                break;

            case 'Stmt_ClassMethod' :
                if ($node->isPublic()) {
                    $this->currentMethod = $node->name;
                    $this->enterPublicMethodNode($node);
                }
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function leaveNode(\PHPParser_Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_Class':
            case 'Stmt_Interface';
                $this->currentClass = false;
                break;

            case 'Stmt_ClassMethod' :
                $this->currentMethod = false;
                break;

            case 'PhpFile' :
                if ($this->currentPhpFile->isModified()) {
                    return $this->currentPhpFile;
                }
                break;
        }
    }

    /**
     * the vertex name for a MethodVertex
     *
     * @return string
     */
    protected function getCurrentMethodIndex()
    {
        return $this->currentClass . '::' . $this->currentMethod;
    }

    /**
     * Extracts annotations in the comment of a statement and injects them in
     * attribute of the node
     *
     * @param \PHPParser_Node_Stmt_ClassMethod $node
     */
    protected function extractAnnotation(\PHPParser_Node_Stmt $node)
    {
        if ($node->hasAttribute('comments')) {
            $compil = array();
            foreach ($node->getAttribute('comments') as $comm) {
                preg_match_all('#^.*@mondrian\s+([\w]+)\s+([^\s]+)\s*$#m', $comm->getReformattedText(), $match);
                foreach ($match[0] as $idx => $matchedOccur) {
                    $compil[$match[1][$idx]][] = $match[2][$idx];
                }
            }
            // if there are annotations, we add them to the node
            foreach ($compil as $attr => $lst) {
                $node->setAttribute($attr, $lst);
            }
        }
    }

}