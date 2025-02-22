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
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Client\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponses;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(public AuthServiceInterface $authService, public UserInterface $repository)
    {
        $this->middleware('auth:api')
            ->except(['login', 'register']);
    }

    public function login(LoginRequest $request)
    {
        $response =
            $this->authService->attemptLogin(
                $request->only(['email', 'password']),
                $request->remember
            );

        if (@$response['error']) {
            return response()->error(...$response);
        }
        return response()->success(...$response);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = request()->user();
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
            data: request()->user(),
        );
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $data = $this->authService->refreshToken();
        if (@$data['error']) {
            return response()->success(
                message: $data['message'],
                status: $data['status'],
            );
        }
        return response()->success(
            data: $data,
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