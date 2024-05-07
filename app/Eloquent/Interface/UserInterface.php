<?php

namespace App\Eloquent\Interface;

use App\Models\User;

interface UserInterface
{
    public function update($request, int $userId): User|null;
    public function create($request): User;
    public function findOneBy(array $conditions): User|null;
}