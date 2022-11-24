<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attribute;
use Illuminate\Support\Str;

class AttributesController extends Controller
{
    public function show($id)
    {
        return Attribute::where('id', $id)->with('product:name,id,slug')->first();
    }

    public function store(Request $request)
    {
        $slug = Str::slug($request->size);
        $attribute = Attribute::query()
            ->create(array_merge($this->validateRequest($request), ['slug' => $slug]))->fresh();       

        return Attribute::query()
            ->find($attribute->id);
    }

    public function update(Request $request, Attribute $attribute)
    {
        $this->validateRequest($request);
        Attribute::where('id', $attribute->id)->update(
            [
                'images' => $request->images,
                'price' => $request->price,
                'size' => $request->size,
                'slug' => Str::slug($request->size),
                'description' => $request->description,
                'published' => $request->published
            ]
        );

        $res = Attribute::where('id', $attribute->id)->first();
        return $res;
    }

    public function destroy($id)
    {
        Attribute::find($id)->delete();
    }

    private function validateRequest($request)
    {
        return $request->validate([
            'product_id' => 'required',
            "images"    => "required|json",
            'price' => 'required|integer',
            'size' => 'required|string',
            'description' => 'nullable|json',
            'published' => 'required',
        ]);
    }
}
