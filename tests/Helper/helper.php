<?php

namespace Mizz\Belajar\PHP\MVC\App {
    function header(string $value)
    {
        echo $value;
    }
}

namespace Mizz\Belajar\PHP\MVC\Service {
    function setcookie(string $nameCookie, string $valueCookie)
    {
        echo "$nameCookie: $valueCookie";
    }
}