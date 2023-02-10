<?php

use Mizz\Belajar\PHP\MVC\Config\Database;
use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Mizz\Belajar\PHP\MVC\Domain\User;
use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    public function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = 'mizz';
        $user->name = 'mizz kun';
        $user->password = password_hash('mizz', PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->password, $result->password);
    }

    public function testFindByIdNotFound()
    {
        $user =  $this->userRepository->findById('notfound');
        self::assertNull($user, 'null');
    }

    public function testUpdate()
    {
        $user = new User();
        $user->id = 'mizz';
        $user->name = 'mizz kun';
        $user->password = 'mizz';

        $this->userRepository->save($user);

        $user->name = 'Jani Chan';
        $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->password, $result->password);
    }
}
