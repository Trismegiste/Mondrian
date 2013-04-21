<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Refactor\Refactored;
use Trismegiste\Mondrian\Refactor\RefactorPass;

/**
 * ParamRefactor replaces the class of a param by its contract
 *
 */
class ParamRefactor extends FqcnHelper implements RefactorPass
{

    protected $context;
    private $isDirty = false;

    public function __construct(Refactored $ctx)
    {
        $this->context = $ctx;
    }

    public function beforeTraverse(array $nodes)
    {
        parent::beforeTraverse($nodes);

        $this->isDirty = false;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        if ($node->getType() === 'Param') {
            $this->enterParam($node);
        }
    }

    protected function enterParam(\PHPParser_Node_Param $node)
    {
        if ($node->type instanceof \PHPParser_Node_Name) {
            $typeHint = (string) $this->resolveClassName($node->type);
            if (array_key_exists($typeHint, $this->context->newContract)) {
                $node->type = new \PHPParser_Node_Name_FullyQualified($this->context->newContract[$typeHint]);
                $this->isDirty = true;
            }
        }
    }

    public function isModified()
    {
        return $this->isDirty;
    }

    public function hasGenerated()
    {
        return false;
    }

    public function getGenerated()
    {
        
    }

}