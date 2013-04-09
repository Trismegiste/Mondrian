<?php

/*
 * To test if same method name well filtered
 */

namespace Project;

class SomeService
{

    public function getTitle()
    {
        return 'ze name';
    }

}

class OtherClass
{

    public function getTitle()
    {
        return 'Coincidental nmethod name';
    }

}

class FilterCalling
{

    public function decorate3()
    {
        // must generate two edges
        $this->obj->getTitle();
    }

    /**
     * @mondrian ignoreCallTo Project\OtherClass::getTitle
     */
    public function decorate2()
    {
        // should generate two edges but annotation remove ones
        $this->obj->getTitle();
    }

}