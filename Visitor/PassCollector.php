<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\Context;
use Trismegiste\Mondrian\Transform\CompilerPass;

/**
 * PassCollector is an abstract compiler pass for visiting source code
 */
abstract class PassCollector extends \PHPParser_NodeVisitor_NameResolver implements CompilerPass
{

    protected $graph;
    protected $vertex;
    protected $inheritanceMap;

    public function __construct(Context $ctx)
    {
        $this->graph = $ctx->graph;
        $this->vertex = &$ctx->vertex;
        $this->inheritanceMap = &$ctx->inheritanceMap;
    }

    /**
     * Search if a type (class or interface) exists in the inheritanceMap
     *
     * @param string $cls
     * @return bool
     */
    protected function hasDeclaringClass($cls)
    {
        return array_key_exists($cls, $this->inheritanceMap);
    }

    /**
     * Finds the FQCN of the first declaring class/interface of a method
     *
     * @param string $cls subclass name
     * @param string $meth method name
     * @return string
     */
    protected function getDeclaringClass($cls, $meth)
    {
        return $this->inheritanceMap[$cls]['method'][$meth];
    }

    /**
     * Is FQCN an interface ?
     *
     * @param string $cls FQCN
     * @return bool
     */
    protected function isInterface($cls)
    {
        return $this->inheritanceMap[$cls]['interface'];
    }

}