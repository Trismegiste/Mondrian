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

    public function setMeta($key, $val);

    public function getMeta($key);

    public function hasMeta($key);
}
