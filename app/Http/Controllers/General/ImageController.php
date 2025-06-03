<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Services\FileService;
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

    // upload file
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,gif,webp,mp4,mov,avi,mkv,mp3,wav,m4a,ogg,webm,txt,csv,json,xml,zip,rar,7z,tar,gz,bz2,iso,dmg,pkg,deb,rpm,exe,app,msi,cab,jar,war,ear,whl,whl.gz,whl.zip,whl.whl,whl.tar,whl.gz.tar,whl.zip.tar,whl.whl.tar,whl.gz.zip,whl.zip.zip,whl.whl.zip,whl.gz.whl,whl.zip.whl,whl.whl.whl',
            'folder' => 'required|string|in:services,users,salons,ads',
        ]);


        $fileName = FileService::storeFile($request->file, $request->folder);

        return response()->json([
            'success' => true,
            'data' => [
                'file_name' => $fileName,
                'file_url' => asset('storage/' . $fileName),
            ],
            'message' => 'تم رفع الملف بنجاح',
        ]);
    }

}