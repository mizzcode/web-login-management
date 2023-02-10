<?php

namespace Mizz\Belajar\PHP\MVC\Controller {

    use Mizz\Belajar\PHP\MVC\Config\Database;
    use Mizz\Belajar\PHP\MVC\Domain\Session;
    use Mizz\Belajar\PHP\MVC\Domain\User;
    use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;
    use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
    use Mizz\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class HomeControllerTest extends TestCase
    {
        private HomeController $homeController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->homeController = new HomeController();
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        function testGuest()
        {
            $this->homeController->index();

            $this->expectOutputRegex("[Login Management]");
        }

        function testUserLogin()
        {
            $user = new User();
            $user->id = 'mizz';
            $user->name = 'Mizz Kun';
            $user->password = 'rahasia';
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            
            $this->homeController->index();
        
            $this->expectOutputRegex("[Bonjour! Mizz Kun]");
        }
    }
}