<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use PHPParser_NodeVisitor_NameResolver;

/**
 * ParamRefactor replaces the class of a param by its contract
 *
 */
class ParamRefactor extends PHPParser_NodeVisitor_NameResolver
{

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
            print_r((string) $node->type);
        }
    }

}