<?php

namespace Mizz\Belajar\PHP\MVC\Middleware {

    require_once __DIR__ . "/../Helper/helper.php";

    use Mizz\Belajar\PHP\MVC\Config\Database;
    use Mizz\Belajar\PHP\MVC\Domain\Session;
    use Mizz\Belajar\PHP\MVC\Domain\User;
    use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
    use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;
    use Mizz\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustNotLoginTest extends TestCase
    {

        private MustNotLogin $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->middleware = new MustNotLogin();
            putenv('mode=test');

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        function testBefore()
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

            $this->middleware->before();

            $this->expectOutputString("");

            // kalau user session nya ada, tidak boleh ke halaman login/register
            $this->expectOutputRegex("[Location: /]");
        }
    }
}