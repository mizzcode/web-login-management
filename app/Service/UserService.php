<?php

namespace Mizz\Belajar\PHP\MVC\Service;

use Exception;
use Mizz\Belajar\PHP\MVC\Config\Database;
use Mizz\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Mizz\Belajar\PHP\MVC\Model\UserRegisterResponse;
use Mizz\Belajar\PHP\MVC\Repository\UserRepository;
use Mizz\Belajar\PHP\MVC\Domain\User;
use Mizz\Belajar\PHP\MVC\Exception\ValidationException;
use Mizz\Belajar\PHP\MVC\Model\UserLoginRequest;
use Mizz\Belajar\PHP\MVC\Model\UserLoginResponse;
use Mizz\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use Mizz\Belajar\PHP\MVC\Model\UserPasswordUpdateResponse;
use Mizz\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use Mizz\Belajar\PHP\MVC\Model\UserProfileUpdateResponse;

class UserService
{
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user !== null) {
                throw new ValidationException('ID sudah terdaftar di database');
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            Database::commitTransaction();

            $response = new UserRegisterResponse();
            $response->user = $user;
            return $response;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null || trim($request->id) == '' || trim($request->name) == '' || trim($request->password) == '') {
            throw new ValidationException('Id atau Name atau Password tidak boleh kosong');
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException('Sorry! ID or Password is wrong');
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException('Sorry! ID or Password is wrong');
        }
    }

    public function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null || trim($request->id) == '' || trim($request->password) == '') {
            throw new ValidationException('Id atau Password tidak boleh kosong');
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUserUpdateProfileRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException('Sorry! ID Not Found');
            }
            $user->name = $request->name;
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserUpdateProfileRequest(UserProfileUpdateRequest $request)
    {
        if ($request->id == null || $request->name == null || trim($request->id) == '' || trim($request->name) == '') {
            throw new ValidationException('Id atau Name tidak boleh kosong');
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
    {
        $this->validateUserPasswordRequest($request);
        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException('Sorry! ID Not Found');
            }

            if (!password_verify($request->oldPassword, $user->password)) {
                throw new ValidationException('Old Password is wrong');
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserPasswordRequest(UserPasswordUpdateRequest $request)
    {
        if ($request->id == null || $request->oldPassword == null || $request->newPassword == null || trim($request->id) == '' || trim($request->oldPassword) == '' || trim($request->newPassword) == '') {
            throw new ValidationException('Old Password atau New Password tidak boleh kosong');
        }
    }
}
