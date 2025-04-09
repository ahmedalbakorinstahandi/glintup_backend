<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salons\Auth\ForgetPasswordRequest;
use App\Http\Requests\Salons\Auth\LoginRequest;
use App\Http\Requests\Salons\Auth\RegisterRequest;
use App\Http\Requests\Salons\Auth\RestPasswordRequest;
use App\Http\Requests\Salons\Auth\VerifyCodeRequest;
use App\Http\Resources\Users\UserResource;
use App\Http\Services\Salons\SalonAuthService;
use App\Models\Users\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class SalonAuthController extends Controller
{
    protected $salonAuthService;

    public function __construct(SalonAuthService $salonAuthService)
    {
        $this->salonAuthService = $salonAuthService;
    }


    public function login(LoginRequest $request)
    {
        $loginUserData = $request->validated();

        $user = $this->salonAuthService->login($loginUserData);

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
        $user = $this->salonAuthService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.account_created_verify_phone'),
            'data' => new UserResource($user),
        ], 201);
    }



    public function verifyCode(VerifyCodeRequest $request)
    {
        $user =   $this->salonAuthService->verifyCode($request->validated());

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
        $res = $this->salonAuthService->forgotPassword($request->validated());

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

        $res = $this->salonAuthService->resetPassword($request->validated());

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

        $this->salonAuthService->logout($token);

        return response()->json([
            'success' => true,
            'message' => trans('messages.user_logged_out_successfully'),
        ], 200);
    }
}
