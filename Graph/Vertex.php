<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * Vertex is a vertex
 */
class Vertex
{

    protected $name;
//    protected $content;

    /**
     * Construct the vertex with its name (preferably unique)
     * 
     * @param string $name 
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

//    public function setContent($data)
//    {
//        $this->content = $data;
//    }
//
//    public function getContent()
//    {
//        return $this->content;
//    }

}