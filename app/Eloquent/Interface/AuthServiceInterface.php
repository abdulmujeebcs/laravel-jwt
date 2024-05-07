<?php

namespace App\Eloquent\Interface;

interface AuthServiceInterface
{
    public function attemptLogin(array $credentials, $remember): array;
    public function getAccessToken(): string;
    public function refreshToken(): array;
    public function logout();

}