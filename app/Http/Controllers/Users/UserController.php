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
use App\Models\General\Setting;
use App\Models\Salons\Salon;
use App\Models\Statistics\PromotionAd;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Log;

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
            'data' => UserResource::collection(resource: $data['data']->items()),
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


        # TODO

        $trendingSalons = Salon::where('is_approved', true)
            ->where('is_active', true)
            ->withCount(['bookings' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(14))
                    ->where('status', 'completed');
            }])
            ->orderBy('bookings_count', 'desc')
            ->having('bookings_count', '>', 0)
            ->limit(2)
            ->get();

        $salons_have_discount = Salon::where('is_approved', true)
            ->where('is_active', true)
            ->whereHas('services', function ($query) {
                $query->where('discount_percentage', '>', 0)
                      ->where('is_active', true);
            })
            ->with(['services' => function($query) {
                $query->where('discount_percentage', '>', 0)
                      ->where('is_active', true)
                      ->orderBy('discount_percentage', 'desc');
            }])
            ->withMax('services', 'discount_percentage')
            ->orderByDesc('services_max_discount_percentage')
            ->limit(2)
            ->get();

        // للتأكد من وجود صالونات
        if ($salons_have_discount->isEmpty()) {
            Log::info('No salons with discounts found');
        }

        // request()->merge(['filter_provider' => 'discount']);

        $nearby_salons = Salon::where('is_approved', true)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(2)
            ->get();

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
