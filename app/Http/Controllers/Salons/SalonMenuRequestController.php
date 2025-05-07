<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Permissions\Salons\SalonMenuRequestPermission;
use App\Http\Requests\Salons\SalonMenuRequest\CreateRequest;
use App\Http\Requests\Salons\SalonMenuRequest\UpdateRequest;
use App\Http\Resources\Salons\SalonMenuRequestResource;
use App\Http\Services\Salons\SalonMenuRequestService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class SalonMenuRequestController extends Controller
{
    protected $salonMenuRequestService;

    public function __construct(SalonMenuRequestService $salonMenuRequestService)
    {
        $this->salonMenuRequestService = $salonMenuRequestService;
    }

    public function index(Request $request)
    {
        $menuRequests = $this->salonMenuRequestService->index($request->all());

        return response()->json([
            'success' => true,
            'data' => SalonMenuRequestResource::collection($menuRequests->items()),
            'meta' => ResponseService::meta($menuRequests),
        ]);
    }

    public function show($id)
    {


        $menuRequest = $this->salonMenuRequestService->show($id);

        SalonMenuRequestPermission::show($menuRequest);


        return response()->json([
            'success' => true,
            'data' => new SalonMenuRequestResource($menuRequest),
        ]);
    }


    public function create(CreateRequest $request)
    {

        SalonMenuRequestPermission::create($request->validated());

        $data = $this->salonMenuRequestService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'salon_menu_request.created',
            'data' => $data,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {

        $menuRequest = $this->salonMenuRequestService->show($id);

        $data = $this->salonMenuRequestService->update($menuRequest, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'salon_menu_request.updated',
            'data' => new SalonMenuRequestResource($data),
        ]);
    }

    public function destroy($id)
    {
        $menuRequest = $this->salonMenuRequestService->show($id);

        $this->salonMenuRequestService->destroy($menuRequest);

        return response()->json([
            'success' => true,
            'message' => 'salon_menu_request.deleted',
        ]);
    }
}
