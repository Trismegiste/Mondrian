<?php

namespace Project;

interface IOne {}
interface ITwo {}
class Mother {}

class Root extends Mother implements IOne, ITwo
{

}
