<?php

namespace Mizz\Belajar\PHP\MVC\Middleware {
    
    require __DIR__ . "/../Helper/helper.php";

    use PHPUnit\Framework\TestCase;


    class MustLoginTest extends TestCase
    {
        private MustLogin $middleware;

        protected function setUp(): void
        {
            $this->middleware = new MustLogin();
            putenv('mode=test');
        }

        public function testBefore()
        {
            $this->middleware->before();

            $this->expectOutputString("");
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Location: /users/login]");
        }
    }
}