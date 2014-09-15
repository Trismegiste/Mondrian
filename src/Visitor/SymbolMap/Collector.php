<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\SymbolMap;

use Trismegiste\Mondrian\Visitor\VisitorGateway;
use Trismegiste\Mondrian\Transform\ReflectionContext;
use Trismegiste\Mondrian\Transform\GraphContext;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * Collector is ...
 */
class Collector extends VisitorGateway
{

    public function __construct(ReflectionContext $ref, GraphContext $grf, Graph $g)
    {
        $visitor = [
            new \Trismegiste\Mondrian\Visitor\State\PackageLevel(),
            new FileLevel(),
            new ClassLevel(),
            new InterfaceLevel(),
            new TraitLevel()
        ];

        parent::__construct($visitor, $ref, $grf, $g);
    }

    public function afterTraverse(array $dummy)
    {
        $this->reflectionCtx->resolveSymbol();
    }

}