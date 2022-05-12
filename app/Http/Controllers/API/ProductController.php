<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Helpers\ResponseFormatter;
class ProductController extends Controller
{
    public function all(Request $request)
    {

        $id = $request->input('id');
        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');
        
        if($id){
            $product = Product::with(['category', 'galleries'])->find($id);

            if($product){
                return ResponseFormatter::success(
                    $product,
                    'Data Produk Behasil di ambil'
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data Produk Tidak ada',
                    404
                );
            }
        }

        $product = Product::with(['category', 'galleries']);

        if($name) {
            $product->where('name', 'like', '%' . $name . '%');
        }
        if($description) {
            $product->where('description', 'like', '%' . $name . '%');
        }
        if($price_from) {
            $product->where('price', '>=',$price_from);
        }
        if($price_to) {
            $product->where('price', '<=',$price_to);
        }
        if($categories) {
            $product->where('categories',$categories);
        }

        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data Produk berhasil diambil'
        );
    }
}
