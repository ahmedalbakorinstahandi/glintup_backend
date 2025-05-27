<?php

namespace App\Http\Controllers\Users;


use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Contact\CreateRequest;
use App\Http\Requests\Users\Contact\UpdateRequest;
use App\Http\Permissions\Users\ContactPermission;
use App\Http\Resources\Users\ContactResource;
use App\Http\Services\Users\ContactService;
use App\Services\ResponseService;

class ContactController extends Controller
{


    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }


    public function index()
    {
        $items = $this->contactService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => ContactResource::collection($items->items()),
            'meta' => ResponseService::meta($items),
        ]);
    }

    public function show($id)
    {
        $item = $this->contactService->show($id);
        ContactPermission::canShow($item);

        return response()->json([
            'success' => true,
            'data' => new ContactResource($item),
        ]);
    }

    public function store(CreateRequest $request)
    {
        $data = ContactPermission::create($request->validated());
        
        $item = $this->contactService->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.contact.item_created_successfully'),
            'data' => new ContactResource($item),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $item = $this->contactService->show($id);
        ContactPermission::canUpdate($item, $request->validated());
        $item = $this->contactService->update($item, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.contact.item_updated_successfully'),
            'data' => new ContactResource($item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->contactService->show($id);
        ContactPermission::canDelete($item);

        $deleted = $this->contactService->delete($item);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.contact.item_deleted_successfully')
                : trans('messages.contact.failed_delete_item'),
        ]);
    }
}
