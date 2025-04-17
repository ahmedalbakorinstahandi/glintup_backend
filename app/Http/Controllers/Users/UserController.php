<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\User\CreateRequest;
use App\Http\Requests\Users\User\UpdateProfileRequest;
use App\Http\Requests\Users\User\UpdateRequest;
use App\Http\Permissions\Users\UserPermission;
use App\Http\Resources\Salons\SalonResource;
use App\Http\Resources\Statistics\PromotionAdResource;
use App\Http\Services\Users\UserService;
use App\Http\Resources\Users\UserResource;
use App\Http\Services\Statistics\PromotionAdService;
use App\Models\Salons\Salon;
use App\Models\Statistics\PromotionAd;
use App\Services\ResponseService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $data = $this->userService->index(request()->all());

        return response()->json([
            'success' => true,
            'info' => $data['info'],
            'data' => UserResource::collection($data['data']->items()),
            'meta' => ResponseService::meta($data['data']),
        ]);
    }

    public function show($id)
    {
        $user = $this->userService->show($id);

        UserPermission::canShow($user);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = UserPermission::create($request->validated());

        $user = $this->userService->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.user.item_created_successfully'),
            'data' => new UserResource($user),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $user = $this->userService->show($id);

        UserPermission::canUpdate($user, $request->validated());

        $user = $this->userService->update($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.user.item_updated_successfully'),
            'data' => new UserResource($user),
        ]);
    }

    public function destroy($id)
    {
        $user = $this->userService->show($id);

        UserPermission::canDelete($user);

        $deleted = $this->userService->destroy($user);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.user.item_deleted_successfully')
                : trans('messages.user.failed_delete_item'),
        ]);
    }

    public function getProfile()
    {
        $user = $this->userService->getProfile();

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->userService->updateProfile($request->validated());

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function homeData()
    {

        // $prmomtionAdService = new PromotionAdService();

        // $prmomtionAds = $prmomtionAdService->index([
        //     'limit' => 10,
        //     'is_active' => 1,
        //     // 'valid_from_from' => now(),
        //     // 'valid_to_to' => now(),
        // ]);


        $prmomtionAds = PromotionAd::where('valid_from', '<=', now())
            ->where('valid_to', '>=', now())
            ->where('is_active', 1)->get();


        $trendingSalons = Salon::inRandomOrder()->limit(2)->get();
        $salons_have_discount = Salon::inRandomOrder()->limit(2)->get();
        $nearby_salons = Salon::inRandomOrder()->limit(2)->get();





        return response()->json([
            'success' => true,
            'data' => [
                'promotion_ads' => PromotionAdResource::collection($prmomtionAds),
                'trending_salons' => SalonResource::collection($trendingSalons),
                'salons_have_discount' => SalonResource::collection($salons_have_discount),
                'nearby_salons' => SalonResource::collection($nearby_salons),
            ],
        ]);
    }
}
