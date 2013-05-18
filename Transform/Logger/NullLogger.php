<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Logger;

/**
 * NullLogger logs nothing
 */
class NullLogger implements LoggerInterface
{

    public function logCallTo($callee, $called)
    {
        
    }

}