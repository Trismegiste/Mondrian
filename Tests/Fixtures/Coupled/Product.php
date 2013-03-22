<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures\Coupled;

/**
 * Product use too many methods without interface
 */
class Product
{

    /**
     * @interface Pricable
     */
    public function getPrice()
    {
        return 1300;
    }

    /**
     * @interface Pricable
     */
    public function getVAT()
    {
        return 0.2 * $this->getPrice();
    }

    /**
     * @interface Descriptable
     */
    public function getTitle()
    {
        return "EOS 7D";
    }

    /**
     * @interface Descriptable
     */
    public function getDescription()
    {
        return "the best small sensor DSLR of all times";
    }

    /**
     * @interface Categorizable
     */
    public function getCategory()
    {
        return "photography";
    }

    /**
     * @interface Shippable
     */
    public function getWeight()
    {
        return 700;
    }

    /**
     * @interface Shippable
     */
    public function getDimension()
    {
        return array(17, 17, 10);
    }

}