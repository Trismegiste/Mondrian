<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures\Coupled;

/**
 * Cart is coupled to product
 */
class Cart
{

    protected $content = array();

    public function addItem(Product $obj)
    {
        $this->content[] = $obj;
    }

    public function getPrice()
    {
        $sum = 0;
        foreach ($this->content as $item) {
            $sum += $item->getPrice();
        }
        return $sum;
    }

    public function getVAT()
    {
        $sum = 0;
        foreach ($this->content as $item) {
            $sum += $item->getVAT();
        }
        return $sum;
    }

}