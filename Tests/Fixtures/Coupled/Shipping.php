<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures\Coupled;

class Shipping
{

    public function getCost(Product $obj)
    {
        return array_sum($obj->getDimension()) + $obj->getWeight() / 10;
    }

}