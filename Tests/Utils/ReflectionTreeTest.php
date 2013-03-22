<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Utils;

use Trismegiste\Mondrian\Utils\ReflectionTree;

/**
 * ReflectionTreeTest is ...
 *
 * @author flo
 */
class ReflectionTreeTest extends \PHPUnit_Framework_TestCase
{

    public function testMother()
    {
        $refl = new ReflectionTree(__NAMESPACE__ . '\Daughter');
        $this->assertEquals('Trismegiste\Mondrian\Tests\Utils\Mother', $refl->findFirstDeclaration('setter')->name);
    }

    public function testRoot()
    {
        $refl = new ReflectionTree(__NAMESPACE__ . '\Daughter');
        $this->assertEquals('Trismegiste\Mondrian\Tests\Utils\Root', $refl->findFirstDeclaration('getter')->name);
    }

    public function testOverridenMethod()
    {
        $refl = new ReflectionTree(__NAMESPACE__ . '\Daughter');
        $this->assertEquals('Trismegiste\Mondrian\Tests\Utils\Root', $refl->findFirstDeclaration('over')->name);
    }

}

/*
 * 
 * Some fixtures
 * 
 */

interface Root
{

    function getter();

    function over();
}

interface Contract extends Root, Noise1
{
    
}

interface Noise1
{
    
}

interface Noise2
{
    
}

abstract class Mother implements Noise2, Contract
{

    public function over()
    {
        
    }

    public function setter()
    {
        
    }

}

class Daughter extends Mother
{

    public function over()
    {
        
    }

    public function getter()
    {
        
    }

}