<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Permissions\General\AddressPermission;
use App\Http\Requests\General\Address\CreateRequest;
use App\Http\Resources\General\AddressResource;
use App\Http\Services\General\AddressService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    private $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function index(Request $request)
    {
        $data = $request->all();


        $addresses = $this->addressService->index($data);


        return response()->json(
            [
                'success' => true,
                'data' => AddressResource::collection($addresses->items()),
                'meta' => ResponseService::meta($addresses)
            ]
        );
    }

    public function create(CreateRequest $request)
    {
        $data = $request->validated();

        $data = AddressPermission::create($data);

        $address = $this->addressService->create($data);

        return response()->json(
            [
                'success' => true,
                'data' => new AddressResource($address),
                'message' => __('messages.address.created_successfully')
            ]
        );
    }

    public function delete($id)
    {

        $address = $this->addressService->show($id);

        $address = $this->addressService->delete($address);

        return response()->json(
            [
                'success' => true,
                'message' => __('messages.address.deleted_successfully')
            ]
        );
    }
}
