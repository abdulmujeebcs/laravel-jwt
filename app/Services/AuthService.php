<?php

namespace App\Services;

use App\Eloquent\Interface\AuthServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as HttpResponses;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService implements AuthServiceInterface
{

    public function __construct(public User $user)
    {
    }

    public function attemptLogin(array $credentials, $remember): array
    {
        $success = false;
        $errorMessage = '';
        $expiresAt = null;
        $statusCode = HttpResponses::HTTP_FORBIDDEN;
        $customExpireTime = now()->addMinute()->timestamp;

        if (!$token = JWTAuth::attempt($credentials, ['exp' => $customExpireTime])) {
            $errorMessage = __('auth.invalid-credentials');
            $statusCode = HttpResponses::HTTP_UNPROCESSABLE_ENTITY;
        } else {
            $user = request()->user();

            if ($user->status != $user::ACTIVE) {
                $errorMessage = __('auth.inactive');
            } else if (!$user->email_verified_at) {
                $errorMessage = __('auth.verify-your-email');
                $success = false;
            } else {
                $success = true;
                $expiresAt = Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp;
            }

        }

        if ($success) {
            return [
                'data' => [
                    'token' => $token,
                    'user' => $user,
                    'ttl' => $expiresAt
                ],
                'message' => __('auth.loggedin'),
            ];
        } else {
            return [
                'error' => true,
                'message' => $errorMessage,
                'status' => $statusCode
            ];
        }
    }

    public function logout()
    {
        auth()->logout();
    }

    public function getAccessToken(): string
    {
        return JWTAuth::getToken();
    }
    public function refreshToken(): array
    {
        try {
            $refreshToken = JWTAuth::refresh($this->getAccessToken());
            $expiresAt = Carbon::now()
                ->addMinutes(config('jwt.ttl'))->timestamp;

            return [
                'access_token' => $refreshToken,
                'token_type' => 'bearer',
                'ttl' => $expiresAt
            ];
        } catch (JWTException $e) {
            return [
                'error' => true,
                'message' => 'Unable to refresh token',
                'status' => HttpResponses::HTTP_UNAUTHORIZED
            ];
        }
    }
}