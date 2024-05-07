<?php

namespace App\Services;

use App\Eloquent\Interface\AuthServiceInterface;
use App\Models\User;
use Illuminate\Http\Response as HttpResponses;

class AuthService implements AuthServiceInterface
{

    public function __construct(public User $user)
    {
    }

    public function attemptLogin(array $credentials, $remember): array
    {
        $success = false;
        $errorMessage = '';
        $statusCode = HttpResponses::HTTP_FORBIDDEN;

        if (!$token = auth('api')->attempt($credentials)) {
            $errorMessage = __('auth.invalid-credentials');
            $statusCode = HttpResponses::HTTP_UNPROCESSABLE_ENTITY;
        } else {

            $user = auth('api')->user();

            if ($user->status != $user::ACTIVE) {
                $errorMessage = __('auth.inactive');
            } else if (!$user->email_verified_at) {
                $errorMessage = __('auth.verify-your-email');
                $success = false;
            } else {
                $success = true;
            }

        }

        if ($success) {
            return [
                'token' => $token,
                'user' => $user,
                'error' => false,
                'message' => __('auth.loggedin'),
                'status' => $statusCode
            ];
        } else {
            return [
                'token' => null,
                'user' => null,
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
}