<?php

namespace App\Http\Notifications;

use App\Models\Users\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class ComplaintNotification
{


    public static function newComplaint($complaint) 
    {
        $user = $complaint->user;


        $title = 'notifications.admin.complaint.new_complaint';
        $body = 'notifications.admin.complaint.new_complaint_body';

        $data = [
            'complaint_id' => $complaint->id,
            'full_user_name' => $user->first_name . ' ' . $user->last_name,
            'content' => strlen($complaint->content) > 100 ? substr($complaint->content, 0, 100) . '...' : $complaint->content,
        ];


        $pemissionKey = 'complaints';

        $users = User::where('role', 'admin')->whereHas('adminPermissions', function ($query) use ($pemissionKey) {
            $query->where('key', $pemissionKey);
        })->get();

        // log the admin permissions keys
        $adminPermissions = $users->pluck('adminPermissions')->flatten()->pluck('key');
        Log::info('Admin permissions keys: ' . $adminPermissions);
        

        FirebaseService::sendToTopicAndStorage(
            'role-admin',
            $users->pluck('id'),
            [
                'id' => $complaint->id,
                'type' => 'Complaint',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }
}
