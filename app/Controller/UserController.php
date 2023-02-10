<?php

namespace Mizz\Belajar\PHP\MVC\Controller;

use Mizz\Belajar\PHP\MVC\App\View;
use Mizz\Belajar\PHP\MVC\Config\Database;
use Mizz\Belajar\PHP\MVC\Exception\ValidationException;
use Mizz\Belajar\PHP\MVC\Model\UserLoginRequest;
use Mizz\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use Mizz\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use Mizz\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Mizz\Belajar\PHP\MVC\Repository\SessionRepository;
use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
use Mizz\Belajar\PHP\MVC\Service\SessionService;
use Mizz\Belajar\PHP\MVC\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($userRepository, $sessionRepository);
    }

    public function register()
    {
        View::render('User/register', [
            'title' => 'Page Register',
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect("/users/login");
        } catch (ValidationException $e) {
            View::render('User/register', [
                'title' => 'Page Register',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function login()
    {
        View::render('User/login', [
            'title' => 'Login Page',
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);
            View::redirect("/");
        } catch (ValidationException $e) {
            View::render('User/login', [
                'title' => 'Login Page',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect("/");
    }

    public function updateProfile()
    {
        $user = $this->sessionService->current();

        View::render('User/profile', [
            'title' => 'User Update Profile',
            'user' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        ]);
    }

    public function postUpdateProfile()
    {
        $user = $this->sessionService->current();

        $request = new UserProfileUpdateRequest();
        $request->id = $user->id;
        $request->name = $_POST['name'];
        try {
            $this->userService->updateProfile($request);
            View::redirect('/');
        } catch (ValidationException $e) {
            View::render('User/profile', [
                'title' => 'User Update Profile',
                'error' => $e->getMessage(),
                'user' => [
                    'id' => $user->id,
                    'name' => $_POST['name']
                ]
            ]);
        }
    }

    public function updatePassword()
    {
        $user = $this->sessionService->current();

        View::render('User/password', [
            'title' => 'User Update Password',
            'user' => [
                'id' => $user->id
            ]
        ]);
    }

    public function postUpdatePassword()
    {
        $user = $this->sessionService->current();

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = $_POST['oldPassword'];
        $request->newPassword = $_POST['newPassword'];

        try {
            $this->userService->updatePassword($request);
            View::redirect('/');
        } catch (ValidationException $e) {
            View::render('User/password', [
                'title' => 'User Update Password',
                'error' => $e->getMessage(),
                'user' => [
                    'id' => $user->id
                ]
            ]);
        }
    }
}
