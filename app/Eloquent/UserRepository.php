<?php

namespace App\Eloquent;

use App\Eloquent\Interface\UserInterface;
use App\Models\User;
use Hash;

class UserRepository implements UserInterface
{
    public function __construct(public User $model)
    {
    }

    public function create($requestData): User
    {
        return $this->model->create($requestData);
    }

    public function update($requestData, int $userId): User|null
    {
        $user = $this->model->find($userId);
        if ($user) {
            $user->update($requestData);
            return $user;
        }
        return null;
    }

    public function findOneBy(array $conditions): User|null
    {
        $user = User::where($conditions)->first();
        return $user;
    }
}