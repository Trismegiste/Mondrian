<?php

/*
 * Stub for Box
 */

spl_autoload_register(function ($class) {
            if (preg_match('#^Trismegiste\\\\Mondrian\\\\(.+)$#', $class, $ret)) {
                $relPath = str_replace('\\', DIRECTORY_SEPARATOR, $ret[1]);
                require_once __DIR__ . DIRECTORY_SEPARATOR . $relPath . '.php';
            }
        });

require_once 'mondrian.php';
