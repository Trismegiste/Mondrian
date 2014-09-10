<?php

/*
 * To test if same method name well filtered
 */

namespace Project;

class SomeService implements \IteratorAggregate
{

    public function getIterator()
    {
        return 'ze name';
    }

}

class OtherClass implements \IteratorAggregate
{

    public function getIterator()
    {
        return 'Coincidental method name';
    }

}

class FilterCalling
{

    public function getSpanner()
    {
        return '<span>';
    }

    public function decorate(SomeService $obj)
    {
        $this->getSpanner();  // does not generate an edge
        return $obj->getIterator();  // generate no edge at all
    }

}
