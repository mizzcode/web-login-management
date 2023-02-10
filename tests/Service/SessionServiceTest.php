<?php

namespace Mizz\Belajar\PHP\MVC\Service;

require_once __DIR__ . "/../Helper/helper.php";

use Mizz\Belajar\PHP\MVC\Config\Database;
use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;
use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
use Mizz\Belajar\PHP\MVC\Domain\Session;
use Mizz\Belajar\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->userRepository, $this->sessionRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = 'mizz21';
        $user->name = 'Mizz Kun';
        $user->password = password_hash('mizz21', PASSWORD_BCRYPT);

        $this->userRepository->save($user);
    }

    function testCreate()
    {
        $session = $this->sessionService->create('mizz21');

        $this->expectOutputRegex("[MIZZ-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals($session->userId, $result->userId);
    }

    function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = 'mizz21';
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[MIZZ-SESSION: ]");

        $session = $this->sessionRepository->findById($session->id);

        self::assertNull($session);
    }

    function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = 'mizz21';
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }
}