<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
class ProductCategoryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
        $name = $request->input('name');
        $show_product = $request->input('show_product');

        if($id){
            $category = ProductCategory::with('products')->find($id);

            if($category){
                return ResponseFormatter::success(
                    $category,
                    'Data Kategori Behasil di ambil'
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data Kategori Tidak ada',
                    404
                );
            }
        }

        $category = ProductCategory::query();

        if($name) {
            $category->where('name', 'like', '%' . $name . '%');
        }
        if($show_product) {
            $category->with('products');
        }

        return ResponseFormatter::success(
            $category->paginate($limit),
            'Data Kategori berhasil diambil'
        );
    }
}
