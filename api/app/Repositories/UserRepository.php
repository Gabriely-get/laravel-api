<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserRepository
{
    public function find($id)
    {
        return User::find($id);
    }

    public function findAll()
    {
        return User::all();
    }

    public function create(array $data):JsonResponse|User
    {
        try {
            $user_created = User::create($data);
            return $user_created;
        } catch (\Throwable $th) {
            throw new \RuntimeException($th->getMessage(), 400);
        }
    }
}
