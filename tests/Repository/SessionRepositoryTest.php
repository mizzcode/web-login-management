<?php

namespace Mizz\Belajar\PHP\MVC\Repository;

use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;
use Mizz\Belajar\PHP\MVC\Config\Database;
use Mizz\Belajar\PHP\MVC\Domain\Session;
use Mizz\Belajar\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

class SessionRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = 'mizz21';
        $user->name = 'Mizz Kun';
        $user->password = password_hash('mizz21', PASSWORD_BCRYPT);

        $this->userRepository->save($user);
    }

    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = 'mizz21';
        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);
    }
    public function testDeleteById()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = 'mizz21';
        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);

        $this->sessionRepository->deleteById($session->id);

        $result = $this->sessionRepository->findById($session->id);

        self::assertNull($result);
    }
    public function testFindByIdNotFound()
    {
        $result = $this->sessionRepository->findById('123');

        self::assertNull($result);
    }
}