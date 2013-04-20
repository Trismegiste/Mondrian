<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Refactor;

/**
 * RefactorPass is a contract for a refactoring pass
 */
interface RefactorPass
{

    /**
     * Is this pass has changed the parsed content ?
     * 
     * @return bool
     */
    function isModified();

    function hasGenerated();

    function getGenerated();
}