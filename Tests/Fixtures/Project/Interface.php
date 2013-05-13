<?php

namespace Project;

interface IOne {}
interface ITwo {}
interface IThree extends ITwo {}
interface Multiple extends IOne, ITwo
{

}
