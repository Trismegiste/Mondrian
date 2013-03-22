<?php

namespace Trismegiste\Mondrian\Tests\Fixtures\Graph;

interface IOne {}
interface ITwo {}
class Mother {}

class Root extends Mother implements IOne, ITwo
{
    
}