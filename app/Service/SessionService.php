<?php

namespace Mizz\Belajar\PHP\MVC\Service;

use Mizz\Belajar\PHP\MVC\Domain\Session;
use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;
use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
use Mizz\Belajar\PHP\MVC\Domain\User;

class SessionService
{
    public static string $COOKIE_NAME = 'MIZZ-SESSION';
    
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    public function __construct(UserRepository $userRepository, SessionRepository $sessionRepository)
    {
        $this->userRepository = $userRepository;
        $this->sessionRepository = $sessionRepository;
    }


    public function create(string $userId): Session
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = $userId;

        $this->sessionRepository->save($session);

        setcookie(self::$COOKIE_NAME, $session->id, time() + 3600 * 24, "/");

        return $session;
    }

    public function destroy()
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        
        $this->sessionRepository->deleteById($sessionId);

        setcookie(self::$COOKIE_NAME, '', 1, "/");
    }

    public function current(): ?User
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        
        $session = $this->sessionRepository->findById($sessionId);
        if ($session == null) {
            return null;
        }        
        return $this->userRepository->findById($session->userId);
    }
}