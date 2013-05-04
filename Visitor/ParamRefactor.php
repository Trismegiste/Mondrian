<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Refactor\Refactored;

/**
 * ParamRefactor replaces the class of a param by its contract
 *
 */
class ParamRefactor extends FqcnHelper
{

    protected $context;

    public function __construct(Refactored $ctx)
    {
        $this->context = $ctx;
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
            if ($this->context->hasNewContract($typeHint)) {
                $node->type = new \PHPParser_Node_Name_FullyQualified($this->context->getNewContract($typeHint));
                $this->currentPhpFile->modified();
            }
        }
    }

}