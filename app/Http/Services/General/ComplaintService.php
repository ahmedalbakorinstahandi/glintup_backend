<?php

namespace App\Http\Services\General;

use App\Models\General\Complaint;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\General\ComplaintPermission;
use App\Models\Users\User;

class ComplaintService
{
    public function index($data)
    {
        $query = Complaint::with(['user', 'reviewer']);


        $query = ComplaintPermission::filterIndex($query);


        return FilterService::applyFilters(
            $query,
            $data,
            ['content', 'phone_number'],
            [],
            [],
            ['reviewed_by', 'user_id'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = Complaint::with(['user', 'reviewer'])->find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.complaint.item_not_found');
        }
        return $item;
    }

    public function create($data)
    {
        $complaint = Complaint::create($data);

        return $complaint;
    }

    public function update($item, $data)
    {
        $user = User::auth();

        $data['reviewed_by'] = $user->id;
        $data['reviewed_at'] = now();


        $item->update($data);

        $item->load(['user', 'reviewer']);

        return $item;
    }

    public function destroy($item)
    {
        return $item->delete();
    }
}
