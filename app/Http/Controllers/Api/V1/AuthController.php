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
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Authentication')]

class AuthController extends Controller
{
    public function __construct(public AuthServiceInterface $authService, public UserInterface $repository)
    {
        $this->middleware('auth:api')
            ->except(['login', 'register']);
    }


    #[OA\Post(
        path: '/api/v1/auth/login',
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'User Login'),
            new OA\Response(response: 401, description: 'UnAuthorized'),
            new OA\Response(response: 422, description: 'Invalid Credentials'),
        ]
    )]
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

    #[OA\Put(
        path: '/api/v1/auth/update-profile',
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Update Profile'),
            new OA\Response(response: 401, description: 'UnAuthorized'),
        ]
    )]
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = request()->user();
        $data = $this->repository->update($request->validated(), $user->id);
        return response()->success(
            message: __('auth.profile-updated'),
            data: $data
        );
    }

    #[OA\Get(
        path: '/api/v1/auth/user',
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Get Authenticated User'),
            new OA\Response(response: 401, description: 'UnAuthorized'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/v1/auth/logout',
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'User Logout'),
            new OA\Response(response: 401, description: 'UnAuthorized'),
        ]
    )]
    public function logout()
    {
        $this->authService->logout();

        return response()->success(
            __("auth.logout"),
            [],
            HttpResponses::HTTP_OK
        );
    }

    #[OA\Post(
        path: '/api/v1/auth/register',
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'User Registeration'),
        ]
    )]
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