<?php

namespace Mizz\Belajar\PHP\MVC\Middleware;

interface Middleware
{
    function before(): void;
}