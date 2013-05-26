<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Refactor;

use Trismegiste\Mondrian\Parser\PhpPersistence;
use Trismegiste\Mondrian\Builder\Compiler\AbstractTraverser;

/**
 * RefactoringBuilder builds an abstract builder for refactoring service
 */
abstract class RefactoringBuilder extends AbstractTraverser
{

    protected $dumper;

    public function __construct(PhpPersistence $dump)
    {
        $this->dumper = $dump;
    }

}