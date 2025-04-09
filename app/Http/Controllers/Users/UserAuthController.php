<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Auth\ForgetPasswordRequest;
use App\Http\Requests\Users\Auth\LoginRequest;
use App\Http\Requests\Users\Auth\RegisterRequest;
use App\Http\Requests\Users\Auth\RestPasswordRequest;
use App\Http\Requests\Users\Auth\VerifyCodeRequest;
use App\Http\Resources\Users\UserResource;
use App\Http\Services\Users\UserAuthService;
use App\Models\Users\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class UserAuthController extends Controller
{
    protected $userAuthService;

    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }


    public function login(LoginRequest $request)
    {
        $loginUserData = $request->validated();

        $user = $this->userAuthService->login($loginUserData);

        if (!$user) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => trans('messages.invalid_credentials'),
            ], 401);
        }

        $token = $user->createToken($user->first_name . '-AuthToken')->plainTextToken;

        FirebaseService::subscribeToAllTopic($request, $user);

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'data' => new UserResource($user),
        ]);
    }


    public function register(RegisterRequest $request)
    {
        $user = $this->userAuthService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.account_created_verify_phone'),
            'data' => new UserResource($user),
        ], 201);
    }



    public function verifyCode(VerifyCodeRequest $request)
    {
        $user =   $this->userAuthService->verifyCode($request->validated());

        $token = $user->createToken($user->first_name . '-AuthToken')->plainTextToken;


        return response()->json([
            'success' => true,
            'message' => trans('messages.account_verified_successfully'),
            'access_token' => $token,
            'data' => new UserResource($user),
        ], 201);
    }


    public function forgotPassword(ForgetPasswordRequest $request)
    {
        $res = $this->userAuthService->forgotPassword($request->validated());

        if (!$res) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.phone_not_found'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => trans('messages.otp_sent_successfully', ['phone' => $request->phone]),
        ]);
    }


    public function resetPassword(RestPasswordRequest $request)
    {
        $user = User::auth();

        $res = $this->userAuthService->resetPassword($request->validated());

        if ($res['success'] == true) {

            FirebaseService::subscribeToAllTopic($request, $user);

            return response()->json(
                [
                    'success' => true,
                    'access_token' => $res['token'],
                    'data' => new UserResource($user),
                    'message' => trans('messages.password_reset_successfully'),
                ],
                201,
            );
        }
    }

    public function logout()
    {

        $token = request()->bearerToken();

        $this->userAuthService->logout($token);

        return response()->json([
            'success' => true,
            'message' => trans('messages.user_logged_out_successfully'),
        ], 200);
    }
}
