<?php

namespace Trismegiste\Mondrian\Tests\Fixtures\Graph;

interface IOne {}
interface ITwo {}
interface IThree extends ITwo {}
interface Multiple extends IOne, ITwo
{
    
}