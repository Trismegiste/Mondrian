<?php

/*
 * To test if same method name well filtered with annotations
 */

namespace Project;

// 3 vertices and 3 edges for this class
class SomeService
{

    public function getTitle()
    {
        return 'ze name';
    }

}

// 3 vertices and 3 edges for this class
class OtherClass
{

    public function getTitle()
    {
        return 'Coincidental method name';
    }

}

// 1 vertex for the class
class FilterCalling
{

    // 2 vertices and 3 edges for this method
    public function decorate3()
    {
        // plus 2 edges for this call
        $this->obj->getTitle();
    }

    /**
     * 2 vertices and 3 edges for this method
     *
     * the config in the test ignores the call to Project\OtherClass::getTitle
     */
    public function decorate2()
    {
        // plus 1 edge for this call
        // should generate two edges but config remove ones
        $this->obj->getTitle();
    }

}
