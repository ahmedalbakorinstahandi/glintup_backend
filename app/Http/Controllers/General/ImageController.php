<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'folder' => 'required|string|in:services,users,salons,ads',
        ]);

        $imageName = ImageService::storeImage($request->image, $request->folder);

        return response()->json([
            'success' => true,
            'data' => [
                'image_name' => $imageName,
                'image_url' => asset('storage/' . $imageName),
            ],
            'message' => 'تم رفع الصورة بنجاح',
        ]);
    }

}