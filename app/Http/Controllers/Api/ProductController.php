<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends ApiBaseController
{
    /**
     * List products after user authentication
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::with('images')->get();
        return response()->json($products);
    }
}
