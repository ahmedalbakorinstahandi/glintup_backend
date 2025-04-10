<?php

namespace App\Http\Services\General;

use App\Models\General\Notification;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\General\NotificationPermission;

class NotificationService
{
    public function index($data)
    {
        $query = Notification::query()->with(['user', 'notificationable']);

        $query = NotificationPermission::filterIndex($query);

        return FilterService::applyFilters($query, $data, ['title', 'message'], [], ['created_at'], ['user_id'], ['id']);
    }

    public function show($id)
    {
        $notification = Notification::with(['user', 'notificationable'])->find($id);

        if (!$notification) {
            MessageService::abort(404, 'messages.notification.item_not_found');
        }

        return $notification;
    }

    public function create($validatedData)
    {
        return Notification::create($validatedData);
    }


    public function update($notification, $validatedData)
    {
        $notification->update($validatedData);
        return $notification;
    }

    public function destroy($notification)
    {
        return $notification->delete();
    }


    public static function storeNotification($users_ids, $notificationable, $title, $body, $replace, $data = [])
    {
        $notificationService = new NotificationService();
        $locales = config('translatable.locales');

        foreach ($users_ids as $user_id) {
            $notificationData = [
                'user_id' => $user_id,
                'title' => [],
                'message' => [],
                'notificationable_id' => $notificationable['id'] ?? null,
                'notificationable_type' => $notificationable['type'] ?? 'Custom',
                'metadata' => [
                    'data' => $data,
                    'replace' => $replace,
                    'notificationable' => $notificationable,
                ],
            ];

            foreach ($locales as $locale) {
                $notificationData['title'][$locale] = __($title, $replace, $locale);
                $notificationData['message'][$locale] = __($body, $replace, $locale);
            }

            $notificationService->create($notificationData);
        }
    }
}