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
use App\Http\Services\Salons\SalonService;
use App\Models\General\Setting;
use App\Models\Statistics\PromotionAd;
use App\Services\PermissionHelper;
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

        PermissionHelper::checkAdminPermission('users');
        $data = $this->userService->index(request()->all());

        return response()->json([
            'success' => true,
            'info' => $data['info'],
            'data' => UserResource::collection(resource: $data['data']->items()),
            'meta' => ResponseService::meta($data['data']),
        ]);
    }

    public function show($id)
    {
        PermissionHelper::checkAdminPermission('users');
        $user = $this->userService->show($id);

        UserPermission::canShow($user);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function create(CreateRequest $request)
    {
        PermissionHelper::checkAdminPermission('users');
        
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
        PermissionHelper::checkAdminPermission('users');
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
        PermissionHelper::checkAdminPermission('users');
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

        if ($user->isAdmin()) {
            $user->load(['adminPermissions']);
        }

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
            ->where('is_active', 1)
            ->where('status', 'approved')
            ->get();

        // location
        $latitude = request()->get('latitude');
        $longitude = request()->get('longitude');


        $salonService = new SalonService();
        $trendingSalons = $salonService->index([
            'filter_provider' => 'trending',
            'limit' => 2,
        ]);


        $nearby_salons = $salonService->index([
            'filter_provider' => 'nearby',
            'latitude' => request()->get('latitude') ?? null,
            'longitude' => request()->get('longitude') ?? null,
            'limit' => 2,
        ]);


        $request = request()->merge(['filter_provider' => 'discount']);

        $salons_have_discount = $salonService->index([
            'filter_provider' => 'discount',
            'limit' => 2,
        ]);

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



    public function secondData()
    {

        return response()->json([
            'success' => true,
            'data' => [
                'help' => [
                    'en' => Setting::where('key', 'help_en')->first()->value,
                    'ar' => Setting::where('key', 'help_ar')->first()->value,
                ],
                'terms_and_condition' => [
                    'en' => Setting::where('key', 'terms_and_condition_en')->first()->value,
                    'ar' => Setting::where('key', 'terms_and_condition_ar')->first()->value,
                ],
                'privacy_policy' => [
                    'en' => Setting::where('key', 'privacy_policy_en')->first()->value,
                    'ar' => Setting::where('key', 'privacy_policy_ar')->first()->value,
                ],
                'about_app' => [
                    'en' => Setting::where('key', 'about_app_en')->first()->value,
                    'ar' => Setting::where('key', 'about_app_ar')->first()->value,
                ],
                'contacts' => [
                    'phone' => Setting::where('key', 'phone')->first()->value,
                    'email' => Setting::where('key', 'email')->first()->value,
                ],
            ],
        ]);
    }
}
