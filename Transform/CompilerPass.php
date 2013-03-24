<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

/**
 * CompilerPass is a contract for a compil pass
 */
interface CompilerPass
{

    function compile();
}