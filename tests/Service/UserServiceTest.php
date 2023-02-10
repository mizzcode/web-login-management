<?php

namespace Mizz\Belajar\PHP\MVC\Service;

use Mizz\Belajar\PHP\MVC\Config\Database;
use Mizz\Belajar\PHP\MVC\Exception\ValidationException;
use Mizz\Belajar\PHP\MVC\Model\UserLoginRequest;
use Mizz\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
use Mizz\Belajar\PHP\MVC\Service\UserService;
use PHPUnit\Framework\TestCase;
use Mizz\Belajar\PHP\MVC\Domain\User;
use Mizz\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use Mizz\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->sessionRepository = new SessionRepository($connection);

        $this->userService = new UserService($this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = 'mizz';
        $request->name = 'mizz kun';
        $request->password = 'mizz';

        $response = $this->userService->register($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = '';
        $request->name = '';
        $request->password = 'mizz';

        $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $request = new UserRegisterRequest();
        $request->id = 'mizz';
        $request->name = 'mizz kun';
        $request->password = 'mizz';

        $this->userService->register($request);

        $this->expectException(ValidationException::class);

        $request->id = 'mizz';
        $request->name = 'mizz chan';
        $request->password = 'mizzchan';

        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = 'mizz';
        $request->password = 'mizz';

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = 'mizz21';
        $user->name = 'mizz';
        $user->password = password_hash('mizz', PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = 'mizz';
        $request->password = 'salah';

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = 'mizz21';
        $user->name = 'mizz';
        $user->password = password_hash('mizz', PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserLoginRequest();
        $request->id = 'mizz21';
        $request->password = 'mizz';

        $response = $this->userService->login($request);

        self::assertEquals($request->id, $request->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testLoginValidationError()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = 'mizz21';
        $user->name = 'mizz';
        $user->password = password_hash('mizz', PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserLoginRequest();
        $request->id = 'mizz21';
        $request->password = '';

        $response = $this->userService->login($request);

        self::assertEquals($request->id, $request->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testUpdateProfile()
    {
        $user = new User();
        $user->id = 'mizz21';
        $user->name = 'mizz';
        $user->password = password_hash('mizz', PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->id = $user->id;
        $request->name = 'jani';
        $this->userService->updateProfile($request);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($request->name, $result->name);
    }

    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = 'mizz';
        $user->name = 'Mizz Kun';
        $user->password = password_hash('mizz', PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = 'mizz';
        $request->newPassword = 'jani';
        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);

        self::assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = 'mizz';
        $user->name = 'Mizz Kun';
        $user->password = password_hash('mizz', PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = '';
        $request->newPassword = '';
        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);

        self::assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdatePasswordWorngOldPassword()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = 'mizz';
        $user->name = 'Mizz Kun';
        $user->password = password_hash('mizz', PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = 'salah';
        $request->newPassword = 'jani';
        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);

        self::assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdatePasswordNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = 'mizz';
        $request->oldPassword = 'mizz';
        $request->newPassword = 'jani';
        $this->userService->updatePassword($request);
    }
}