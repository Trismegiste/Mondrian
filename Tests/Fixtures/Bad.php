<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures;

class Noise {}

/**
 * Bad is a sample of a concrete class without any abstraction
 */
class Bad
{

    protected $a;

    /**
     * Construct
     *
     * @param Param $a
     */
    public function __construct(Param $a)
    {
        $this->a = $a;
        $this->b = 42;
    }

    private $b, $c;

    /**
     * Comment
     *
     * @param \stdClass $obj
     * @param type $n
     */
    public function accessor(\stdClass $obj, Param $n)
    {

    }
    
    protected function doNotAbstract() {}
    
    public function otherMethod() {}    

}

class DoNotCollect
{

    public function notCollected()
    {

    }

}