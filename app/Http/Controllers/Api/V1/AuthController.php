<?php

namespace App\Http\Controllers\Api\V1;

use App\Eloquent\Interface\AuthServiceInterface;
use App\Eloquent\Interface\UserInterface;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\V1\ForgetPasswordRequest;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterationRequest;
use App\Http\Requests\V1\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response as HttpResponses;
use Illuminate\Support\Facades\Password;
use Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthController extends Controller
{
    public function __construct(public AuthServiceInterface $authService, public UserInterface $repository)
    {
        $this->middleware('auth:api')
            ->except(['login', 'register']);
    }

    public function login(LoginRequest $request)
    {
        ['message' => $message, 'error' => $error, 'status' => $status, 'user' => $user, 'token' => $token] =
            $this->authService->attemptLogin(
                $request->only(['email', 'password']),
                $request->remember
            );

        if ($error) {
            return response()->error(
                message: $message,
                status: $status
            );
        }
        return response()->success(
            data: [
                'user' => $user,
                'token' => $token,
            ],
            message: $message,
        );
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth('api')->user();
        $data = $this->repository->update($request->validated(), $user->id);
        return response()->success(
            message: __('auth.profile-updated'),
            data: $data
        );
    }

    public function getUser()
    {
        return response()->success(
            message: __('auth.show-profile'),
            data: auth('api')->user(),
        );
    }

    public function logout()
    {
        $this->authService->logout();

        return response()->success(
            __("auth.logout"),
            [],
            HttpResponses::HTTP_OK
        );
    }

    public function register(RegisterationRequest $request)
    {
        $user = $this->repository->create($request->validated());
        event(new Registered($user));
        return response()->success(
            $user,
            __('auth.registered'),
            HttpResponses::HTTP_CREATED
        );
    }
}