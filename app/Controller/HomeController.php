<?php

namespace Mizz\Belajar\PHP\MVC\Controller;

use Mizz\Belajar\PHP\MVC\App\View;
use Mizz\Belajar\PHP\MVC\Config\Database;
use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;
use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
use Mizz\Belajar\PHP\MVC\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();

        $userRepository = new UserRepository($connection);
        $sessionRepository = new SessionRepository($connection);

        $this->sessionService = new SessionService($userRepository, $sessionRepository);
    }
    function index(): void
    {
        $user = $this->sessionService->current();

        if ($user == null) {
            View::render("Home/index", [
                'title' => 'PHP Login Management'
            ]);
        } else {
            View::render("Home/dashboard", [
               'title' => 'Dashboard',
               'user' => [
                'name' => $user->name
               ] 
            ]);
        }
    }
}