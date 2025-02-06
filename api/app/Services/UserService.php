<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService {
    protected $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function all(){
        $users = $this->userRepository->findAll();
        return $users;
    }

    public function create(array $data) {
        try {
            $user = $this->userRepository->create($data);
            return $user;
        } catch (\Throwable $e) {
            throw new \RuntimeException("Something get wrong");
            //logging
        }
    }
}
