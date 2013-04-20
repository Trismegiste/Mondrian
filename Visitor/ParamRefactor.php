<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use PHPParser_NodeVisitor_NameResolver;
use Trismegiste\Mondrian\Refactor\Refactored;

/**
 * ParamRefactor replaces the class of a param by its contract
 *
 */
class ParamRefactor extends PHPParser_NodeVisitor_NameResolver
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
            $typeHint = (string) $node->type;
            if (array_key_exists($typeHint, $this->context->newContract)) {
                $node->type = new \PHPParser_Node_Name_FullyQualified($this->context->newContract[$typeHint]);
            }
        }
    }

    public function afterTraverse(array $nodes)
    {
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default();
        echo $prettyPrinter->prettyPrint($nodes);
    }

}