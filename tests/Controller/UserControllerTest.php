<?php

namespace Mizz\Belajar\PHP\MVC\Controller {
    require_once __DIR__ . "/../Helper/helper.php";

    use Mizz\Belajar\PHP\MVC\Config\Database;
    use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
    use Mizz\Belajar\PHP\MVC\Controller\UserController;
    use Mizz\Belajar\PHP\MVC\Domain\Session;
    use PHPUnit\Framework\TestCase;
    use Mizz\Belajar\PHP\MVC\Domain\User;
    use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;
    use Mizz\Belajar\PHP\MVC\Service\SessionService;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();
            putenv('mode=test');
        }

        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Page Register]');
        }

        function testPostRegisterSuccess()
        {
            $_POST['id'] = 'Jani21';
            $_POST['name'] = 'Jani Chan';
            $_POST['password'] = 'mizz kun';

            $this->userController->postRegister();

            $this->expectOutputRegex('[Location: /users/login]');
        }

        function testPostRegisterValidationError()
        {
            $_POST['id'] = '';
            $_POST['name'] = 'mizz';
            $_POST['password'] = 'mizz';

            $this->userController->postRegister();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Page Register]');
            $this->expectOutputRegex('[Id atau Name atau Password tidak boleh kosong]');
        }
        function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = 'mizz';
            $user->name = 'Mizz';
            $user->password = 'Mizz';

            $this->userRepository->save($user);

            $_POST['id'] = 'mizz';
            $_POST['name'] = 'Jani';
            $_POST['password'] = 'Jani';

            $this->userController->postRegister();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Page Register]');
            $this->expectOutputRegex('[ID sudah terdaftar di database]');
        }

        function testLogin()
        {
            $this->userController->login();

            $this->expectOutputRegex('[Login Page]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Sign On]');
            $this->expectOutputRegex('[Login]');
            $this->expectOutputRegex('[Mizz]');
        }

        function testLoginSuccess()
        {
            $user = new User();
            $user->id = 'mizz21';
            $user->name = 'mizz';
            $user->password = password_hash('mizz', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'mizz21';
            $_POST['password'] = 'mizz';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[MIZZ-SESSION: ]");
        }

        function testLoginUserNotFound()
        {
            $_POST['id'] = 'mizz';
            $_POST['password'] = 'mizz';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login Page]");
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Mizz]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Sorry! ID or Password is wrong]");
            $this->expectOutputRegex("[Sign On]");
        }

        function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = 'mizz21';
            $user->name = 'mizz';
            $user->password = 'mizz';

            $this->userRepository->save($user);

            $_POST['id'] = 'mizz21';
            $_POST['password'] = 'salah';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login Page]");
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Mizz]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Sorry! ID or Password is wrong]");
            $this->expectOutputRegex("[Sign On]");
        }

        function testLogout()
        {
            $user = new User();
            $user->id = 'mizz';
            $user->name = 'mizz kun';
            $user->password = 'mizz';
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[PHP Login Management]");
            $this->expectOutputRegex("[MIZZ-SESSION: ]");
            $this->expectOutputRegex("[Location: /]");
        }

        public function testUpdateProfile()
        {
            $user = new User();
            $user->id = 'mizz';
            $user->name = 'Mizz Kun';
            $user->password = 'mizz';
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updateProfile();

            $this->expectOutputRegex('[Profile]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[mizz]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Mizz Kun]');
        }

        public function testPostUpdateProfile()
        {
            $user = new User();
            $user->id = 'mizz';
            $user->name = 'Mizz Kun';
            $user->password = 'mizz';
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = 'Jani Chan';
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex('[Location: /]');
        }

        public function testUpdatePassword()
        {
            $user = new User();
            $user->id = 'mizz';
            $user->name = 'Mizz Kun';
            $user->password = 'mizz';
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updatePassword();

            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[mizz]');
        }
        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->id = 'mizz';
            $user->name = 'Mizz Kun';
            $user->password = password_hash('mizz', PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'mizz';
            $_POST['newPassword'] = 'eko';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex('[Location: /]');
        }

        public function testUpdatePasswordValidationError()
        {
            $user = new User();
            $user->id = 'mizz';
            $user->name = 'Mizz Kun';
            $user->password = password_hash('mizz', PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = '';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[mizz]');
            $this->expectOutputRegex('[Old Password atau New Password tidak boleh kosong]');
        }

        public function testUpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->id = 'mizz';
            $user->name = 'Mizz Kun';
            $user->password = password_hash('mizz', PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'salah';
            $_POST['newPassword'] = 'jani';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[mizz]');
            $this->expectOutputRegex('[Old Password is wrong]');
        }
    }
}
