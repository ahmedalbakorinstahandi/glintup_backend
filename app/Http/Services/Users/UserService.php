<?php


namespace App\Http\Services\Users;

use App\Models\Users\User;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Users\UserPermission;
use App\Models\Users\WalletTransaction;
use App\Services\LocationService;

class UserService
{
    public function index($data)
    {
        $query = User::query();

        $query = UserPermission::filterIndex($query);


        // if (isset($data['search']) && $data['search'] != '') {
        //     $data['search'] =  str_replace(' ', '', $data['search']);
        //     $query->whereRaw("CONCAT(phone_code, phone) LIKE ?", [$data['search']]);
        // }

        $query = FilterService::applyFilters(
            $query,
            $data,
            [['first_name', 'last_name']],
            [],
            ['created_at'],
            ['role', 'is_active'],
            ['id'],
            false
        );

        $users = $query->get();

        // status "pending", "confirmed", "completed", "cancelled"

        // حساب متوسط الانفاق لكل مستخدم
        $transactions = WalletTransaction::whereIn('user_id', $users->pluck('id'))
            ->where('direction', 'out')
            ->where('status', 'completed')
            ->where('is_refund', 0)
            ->get();

        $total_spending = $transactions->sum('amount'); // مجموع المبالغ المدفوعة
        $active_users_count = $transactions->groupBy('user_id')->count(); // عدد المستخدمين الذين اشتروا
        $average_spending = $active_users_count > 0 ? $total_spending / $active_users_count : 0; // متوسط الانفاق


        $users_status_count = [
            'all_count' => $users->count(),
            'active_count' => $users->where('is_active', 1)->count(),
            'unactive_count' => $users->where('is_active', 0)->count(),
            'average_spending' => $average_spending,
        ];

        return [
            'data' => $query->paginate($data['limit'] ?? 20),
            'info' => $users_status_count,
        ];
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            MessageService::abort(404, 'messages.user.item_not_found');
        }

        return $user;
    }

    public function create($validatedData)
    {

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        if (isset($validatedData['phone'])) {
            $validatedData['phone'] = str_replace(' ', '', $validatedData['phone']);
        }
        if (isset($validatedData['phone_code'])) {
            $validatedData['phone_code'] = str_replace(' ', '', $validatedData['phone_code']);
        }

        // role 
        $validatedData['role'] = 'customer';

        // added by admin
        $validatedData['added_by'] = 'admin';

        $user = User::create($validatedData);

        return $user;
    }

    public function update($user, $validatedData)
    {

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        $user->update($validatedData);
        return $user;
    }

    public function destroy($user)
    {

        return $user->delete();
    }


    public function getProfile()
    {
        $user = User::auth();

        return $user;
    }

    public function updateProfile($validatedData)
    {

        $user = User::auth();

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        if (isset($validatedData['latitude']) && isset($validatedData['longitude'])) {
            $user->latitude = $validatedData['latitude'];
            $user->longitude = $validatedData['longitude'];

            $address = LocationService::getLocationData($validatedData['latitude'], $validatedData['longitude'])['address'] ?? null;
            if ($address) {
                $user->address = $address;
            }
            $user->save();
        }


        $user->update($validatedData);


        return $user;
    }
}
