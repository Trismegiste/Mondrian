<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures\Coupled;

use Trismegiste\Mondrian\Tests\Fixtures\Coupled\Product;

class Custom
{

    public function getCost(Product $obj)
    {
        $obj->getTitle();
        $obj->getDescription();
        return $obj->getVAT();
    }

}