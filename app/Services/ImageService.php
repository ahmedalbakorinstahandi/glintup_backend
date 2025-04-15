<?php

namespace App\Services;

use App\Models\General\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    private static function MakeFolder($folderName)
    {
        $pathFolder = storage_path(sprintf('app/public/%s', $folderName));

        if (!File::isDirectory($pathFolder)) {
            File::makeDirectory($pathFolder, 0755, true, true);
        }
    }


    public static function storeImage($image, $folder, $name = null)
    {
        self::MakeFolder($folder);
        $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
        if ($name) {
            $imageName = $name . '-' . $imageName;
        }
        // $imageName = $name != null ? $name : uniqid() . '.' . $image->getClientOriginalExtension();
        $new_path = storage_path(sprintf('app/public/%s/%s', $folder, $imageName));
        move_uploaded_file($image, $new_path);
        return sprintf('%s/%s', $folder, $imageName);
    }

    public static function updateImage($image, $folder, $oldImageName): string|null
    {
        // return ImageService::deleteImage($oldImageName) ? ImageService::storeImage($image, $folder) : null;
        return ImageService::storeImage($image, $folder);
    }


    public static function deleteImage($imagePath)
    {
        if (strpos($imagePath, asset("storage/")) !== false) {
            $imagePath = str_replace(asset("storage/"), "", $imagePath);
        }

        if (Storage::disk('public')->exists($imagePath)) {
            // return  Storage::disk('public')->delete($imagePath);
            return true;
        }

        return false;
    }


    public static function removeImages($ids)
    {
        if (!is_array($ids)) {
            abort(
                response()->json([
                    'success' => false,
                    'message' =>  'يجب تقديم معرفات الصور كمصفوفة غير فارغة.',
                ], 422),
            );
        }

        $existingImages = Image::whereIn('id', $ids)->get();

        if (count($ids) !== $existingImages->count()) {
            abort(
                response()->json([
                    'success' => false,
                    'message' => 'بعض الصور المطلوب حذفها غير موجودة.',
                ], 422),
            );
        }



        foreach ($existingImages as $image) {
            if (Storage::exists("public/" . $image->path)) {
                Storage::delete("public/" . $image->path);
            }
            $image->delete();
        }
    }
}
