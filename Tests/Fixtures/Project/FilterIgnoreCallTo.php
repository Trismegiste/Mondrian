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

    /**
     * @mondrian ignoreCallTo Project\OtherClass::getTitle
     */
    public function decorate()
    {
        // must generate two edges but annotation remove ones
        $this->obj->getTitle();
    }

}