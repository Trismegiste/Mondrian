<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * PackageLevel is the starting state
 */
class PackageLevel extends AbstractState
{

    /**
     * @inheritdoc
     */
    public function enter(Node $node)
    {
        switch ($node->getType()) {
            case 'PhpFile':
                $this->context->pushState('file', $node);
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'package';
    }

}