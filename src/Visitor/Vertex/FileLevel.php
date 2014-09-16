<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Vertex;

use Trismegiste\Mondrian\Visitor\State\FileLevelTemplate;
use PhpParser\Node\Stmt;

/**
 * FileLevel is a visitor for file level to add vertex of class, trait, interface
 */
class FileLevel extends FileLevelTemplate
{

    protected function enterClassNode(Stmt\Class_ $node)
    {
        $this->factoryPrototype($node, 'class', 'Trismegiste\Mondrian\Transform\Vertex\ClassVertex');
    }

    protected function enterInterfaceNode(Stmt\Interface_ $node)
    {
        $this->factoryPrototype($node, 'interface', 'Trismegiste\Mondrian\Transform\Vertex\InterfaceVertex');
    }

    protected function enterTraitNode(Stmt\Trait_ $node)
    {
        $this->factoryPrototype($node, 'trait', 'Trismegiste\Mondrian\Transform\Vertex\TraitVertex');
    }

    private function factoryPrototype(Stmt $node, $type, $vertexClass)
    {
        $index = $this->getNamespacedName($node);

        if (!$this->getGraphContext()->existsVertex($type, $index)) {
            $factory = new \ReflectionClass($vertexClass);
            $v = $factory->newInstance($index);
            $this->getGraph()->addVertex($v);
            $this->getGraphContext()->indicesVertex($type, $index, $v);
        }
    }

}