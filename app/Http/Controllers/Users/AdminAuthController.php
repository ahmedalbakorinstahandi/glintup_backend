<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salons\Auth\LoginRequest;
use App\Http\Resources\Users\UserResource;
use App\Http\Services\Users\AdminAuthService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    protected $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }


    public function login(LoginRequest $request)
    {
        $loginUserData = $request->validated();

        $user = $this->adminAuthService->login($loginUserData);

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
}
