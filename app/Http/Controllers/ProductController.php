<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('sort')) {
            $sort = $request->get('sort');
            if ($sort === 'price_asc') $query->orderBy('price');
            if ($sort === 'price_desc') $query->orderByDesc('price');
        }

        return response()->json($query->get());
    }
    
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }
}
