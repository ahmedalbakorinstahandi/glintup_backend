<?php


namespace App\Http\Services\Users;

use App\Models\Users\User;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Users\UserPermission;
use App\Models\Users\WalletTransaction;
use App\Services\LocationService;
use App\Services\PhoneService;

class UserService
{
    public function index($data)
    {
        $query = User::query();

        $query->where('role', 'customer');

        if (!empty($data['search'])) {
            $searchRaw = trim($data['search']);
            $searchDigits = preg_replace('/[^0-9]/', '', $searchRaw);

            $query->where(function ($q) use ($searchRaw, $searchDigits) {
                // إذا كان البحث كله أرقام → بحث بالهاتف فقط
                if (is_numeric($searchDigits) && $searchDigits !== '') {
                    $q->whereRaw("REPLACE(CONCAT(REPLACE(phone_code, '+', ''), phone), ' ', '') LIKE ?", ["%{$searchDigits}%"]);
                } else {
                    // إذا كان في حروف → بحث باسم كامل فقط
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchRaw}%"]);
                }
            });
        }




        $users = $query->get();

        $transactions = WalletTransaction::whereIn('user_id', $users->pluck('id'))
            ->where('direction', 'out')
            ->where('status', 'completed')
            ->where('is_refund', 0)
            ->get();

        $total_spending = $transactions->sum('amount');
        $active_users_count = $transactions->groupBy('user_id')->count();
        $average_spending = $active_users_count > 0 ? $total_spending / $active_users_count : 0;

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

        $phoneParts = PhoneService::parsePhoneParts($validatedData['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->where('role', 'customer')
            ->first();

        if ($user) {
            MessageService::abort(400, 'messages.user.phone_already_exists');
        }

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // role 
        $validatedData['role'] = 'customer';

        // added by admin
        $validatedData['added_by'] = 'admin';

        $validatedData['phone'] = $phoneNumber;
        $validatedData['phone_code'] = $countryCode;

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
