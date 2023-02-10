<?php

namespace Mizz\Belajar\PHP\MVC\Middleware;

use Mizz\Belajar\PHP\MVC\Config\Database;
use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;
use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
use Mizz\Belajar\PHP\MVC\Service\SessionService;
use Mizz\Belajar\PHP\MVC\App\View;

class MustNotLogin implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $userRepository = new UserRepository(Database::getConnection());
        $sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionService = new SessionService($userRepository, $sessionRepository);
    }


    function before(): void
    {
        $user = $this->sessionService->current();
        if ($user != null) {
            View::redirect("/");
        }
    }
}