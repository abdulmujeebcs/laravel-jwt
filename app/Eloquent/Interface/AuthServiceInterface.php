<?php

namespace App\Eloquent\Interface;

interface AuthServiceInterface
{
    public function attemptLogin(array $credentials, $remember):array;
    public function logout();

}