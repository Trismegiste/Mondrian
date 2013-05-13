<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

/**
 * MetaInterface is a contract for a container of metadatas
 *
 */
interface MetaInterface
{

    function setMeta($key, $val);

    function getMeta($key);

    function hasMeta($key);
}
