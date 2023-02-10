<?php

namespace Mizz\Belajar\PHP\MVC\Config;

use PHPUnit\Framework\TestCase;

class databaseTest extends TestCase {
    public function testGetConnection() {
        $connection = Database::getConnection();
        self::assertNotNull($connection, 'Terhubung ke MySQL...');
    }

    public function testGetConnectionSingleton() {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertSame($connection1, $connection2);
    }
}