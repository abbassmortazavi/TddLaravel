<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductForGet;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $product = Product::create($request->all());
        return response()->json(new \App\Http\Resources\Product($product), 201);
    }

    public function show($product)
    {
        $product = Product::findOrFail($product);
        return response()->json(new ProductForGet($product) , 200);
    }
    public function update(Request $request  , $product)
    {
        $product = Product::findOrFail($product);
        $product->update([
            'name'=>$request->name,
            'slug'=>Str::slug($request->slug,'-'),
            'price'=>$request->price
        ]);
        return response()->json(new ProductForGet($product) , 200);
    }

    public function delete($product): JsonResponse
    {
        $product = Product::findOrFail($product);
//        dd($product);
        $product->delete();

        return response()->json('Deleted SuccessFully!' , 200);
    }
}
