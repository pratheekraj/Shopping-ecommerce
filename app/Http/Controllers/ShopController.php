<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request){

        $column = 'id';
        $direction = 'DESC';
        $size = $request->query('size', 12); 
        $order = $request->query('order', -1); 
        $min_price = $request->query('min', 1); 
        $max_price = $request->query('max', 15000); 
        $f_brands = trim($request->query('brands', ''));
        $f_categories = trim($request->query('categories', ''));

        switch($order){
            case 1: 
                $column = "created_at";
                $direction = "DESC";
                break;
            case 2: 
                $column = "created_at";
                $direction = "ASC";
                break;
            case 3: 
                $column = "sale_price";
                $direction = "ASC";
                break;
            case 4: 
                $column = "sale_price";
                $direction = "DESC";
                break;
            default : 
                $column = "id";
                $direction = "DESC";
            break;
                        
        }
     
        $brands = Brand::orderBy('name','asc')->get();
        $categories = Category::orderBy('name','ASC')->get();

        // $products = Product::where(function ($query) use ($f_brands) {
        //     $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'".$f_brands."'='");
        // })->where(function ($query) use ($f_brands) {
        //     $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'".$f_brands."'='");
        // })
        // ->orderBy($column, $direction)->paginate($size);

        $products = Product::when($f_brands !== '', function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands));
        })
        ->when($f_categories !== '', function ($query) use ($f_categories) {
            $query->whereIn('category_id', explode(',', $f_categories));
        })
        ->where(function ($query) use ($min_price,$max_price){
            $query->whereBetween('regular_price',[$min_price,$max_price])
            ->orWhereBetween('sale_price',[$min_price,$max_price]);
        })
        ->orderBy($column, $direction)->paginate($size);

        return view('shop',compact('products','size','order','brands','f_brands','categories','f_categories','min_price','max_price'));
    }

    public function product_details($slug){

        $product = Product::where('slug',$slug)->first();
        $rproducts = Product::where('slug','<>',$slug)->get()->take(8);
        return view('details',compact('product','rproducts'));
    }
}
