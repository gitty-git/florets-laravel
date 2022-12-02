<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Support\Str;

class SizesController extends Controller
{
    public function show($id)
    {
        return Size::where('id', $id)->with('product:name,id,slug')->first();
    }

    public function store(Request $request)
    {
        $slug = Str::slug($request->size);
        $size = Size::query()
            ->create(array_merge($this->validateRequest($request), ['slug' => $slug]))->fresh();       

        return Size::query()
            ->find($size->id);
    }

    public function update(Request $request, Size $size)
    {
        $this->validateRequest($request);
        Size::where('id', $size->id)->update(
            [
                'images' => $request->images,
                'price' => $request->price,
                'name' => $request->size,
                'slug' => Str::slug($request->size),
                'description' => $request->description,
                'published' => $request->published
            ]
        );

        $res = Size::where('id', $size->id)->first();
        return $res;
    }

    public function destroy($id)
    {
        Size::find($id)->delete();
    }

    private function validateRequest($request)
    {
        return $request->validate([
            'product_id' => 'required',
            "images"    => "required|json",
            'price' => 'required|integer',
            'name' => 'required|string',
            'description' => 'nullable|json',
            'published' => 'required',
        ]);
    }
}
