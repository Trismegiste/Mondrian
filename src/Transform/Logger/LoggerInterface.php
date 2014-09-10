<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Logger;

/**
 * LoggerInterface is a contract for a logger
 */
interface LoggerInterface
{

    /**
     * Log a call from a method to a method
     * 
     * @param string $callee fqcn::method that makes the invocation
     * @param string $called the called fqcn::method 
     */
    public function logCallTo($callee, $called);
}