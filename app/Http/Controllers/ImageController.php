<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $mainImgPath = '';
        if ($request->main_image) {
            $mainImgPath = Storage::disk('public')->put('images/products/', new File($request->main_image), 'public');
            $mainImgPath = asset('storage/' . $mainImgPath);
        }

        $imgPaths = [];
        if ($request->images) {            
            foreach ($request->images as $key => $img) {
                $imgPath = Storage::disk('public')->put('images/products/', new File($img), 'public');
                $imgPaths[$key] = asset('storage/' . $imgPath);
            }
        }

        return ['main_image' => $mainImgPath, 'images' => $imgPaths];  
    }

    public function destroy(Image $image, Request $request)
    {
        if ($request->images) {
            $res = '';
            foreach ($request->images as $key => $img) {
                $arr = explode('/', $img);
                $imgName = $arr[count($arr) - 1];
                $path = public_path("/storage/images/products/$imgName");
                
                if (file_exists($path)) {
                    unlink($path);
                    $res = $request->images;
                } else {
                    $res = "File $imgName does not exists.";
                }
            }
            return $res;
        }
    }

    private function validateRequest($request)
    {
        return $request->validate([
            'main_image' => "mimes:jpg,jpeg,png,csv,txt,xlx,xls,pdf|max:2048",
            "images"    => "array|size:3",
            'images.*' => 'mimes:jpg,jpeg,png,csv,txt,xlx,xls,pdf|max:2048',
        ]);
    }
}
