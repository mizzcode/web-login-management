<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Mizz\Belajar\PHP\MVC\App\Router;
use Mizz\Belajar\PHP\MVC\Config\Database;
use Mizz\Belajar\PHP\MVC\Controller\HomeController;
use Mizz\Belajar\PHP\MVC\Controller\UserController;
use Mizz\Belajar\PHP\MVC\Middleware\MustNotLogin;
use Mizz\Belajar\PHP\MVC\Middleware\MustLogin;

Database::getConnection('prod');

// Home Controller
Router::add('GET', '/', HomeController::class, 'index');

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLogin::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLogin::class]);

Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLogin::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLogin::class]);

Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLogin::class]);

Router::add('GET', '/users/profile', UserController::class, 'updateProfile', [MustLogin::class]);
Router::add('POST', '/users/profile', UserController::class, 'postUpdateProfile', [MustLogin::class]);

Router::add('GET', '/users/password', UserController::class, 'updatePassword', [MustLogin::class]);
Router::add('POST', '/users/password', UserController::class, 'postUpdatePassword', [MustLogin::class]);

Router::run();