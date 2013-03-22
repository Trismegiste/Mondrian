<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures\Coupled;

/**
 * Inventory is a ...
 *
 * @author florent
 */
class Inventory
{

    public function getStock(Product $obj)
    {
        $obj->getDimension();
    }

}