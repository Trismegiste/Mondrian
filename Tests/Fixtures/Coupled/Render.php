<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures\Coupled;

/**
 * Render is coupled to Product
 */
class Render
{

    public function view(Product $obj)
    {
        echo $obj->getCategory() . PHP_EOL;
        echo $obj->getTitle() . PHP_EOL;
        echo $obj->getDescription() . PHP_EOL;
        echo $obj->getPrice() . PHP_EOL;
        echo $obj->getVAT() . PHP_EOL;
    }

}