<?php

namespace Mizz\Belajar\PHP\MVC\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase {
    public function testRender() {
        View::render('Home/index', [
            'title' => 'PHP Login Management'
        ]);
        
        $this->expectOutputRegex('[PHP Login Management]');
        $this->expectOutputRegex('[html]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Register]');
        $this->expectOutputRegex('[Login Management]');
        $this->expectOutputRegex('[body]');
        $this->expectOutputRegex('[Programmer]');
    }
}