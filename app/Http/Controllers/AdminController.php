<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Colors\Rgb\Channels\Red;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function brands(){
        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view('admin.brands',compact('brands'));
    }

    
    public function add_brand(){
        return view('admin.add-brand');
    }

    public function store_brand(Request $request){

        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:brands,slug',
            'image'=>'mimes:png,jpeg,jpg|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imgExtn = $image->extension();
            $imgName = Carbon::now()->timestamp.'.'.$imgExtn;
            $this->GenerateBrandThumbNailsImage($image,$imgName);
            $brand->image = $imgName;
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('success','Brand has been added successfully');
    }

    public function GenerateBrandThumbNailsImage($image,$imageName){
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    } 

    public function edit_brand($id){
        $brand = Brand::find($id);
        return view('admin.edit-brand',compact('brand'));
    }

    public function update_brand($id, Request $request){
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:brands,slug,'.$request->id,
            'image'=>'mimes:png,jpeg,jpg|max:2048',
        ]);

        $brand = Brand::find($id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/brands/'.$brand->image))){
                File::delete(public_path('uploads/brands/'.$brand->image));
            }
            $image = $request->file('image');
            $imgExtn = $image->extension();
            $imgName = Carbon::now()->timestamp.'.'.$imgExtn;
            $this->GenerateBrandThumbNailsImage($image,$imgName);
            $brand->image = $imgName;
        }
        
        $brand->save();
        return redirect()->route('admin.brands')->with('success','Brand has been updated successfully');
    }

    public function delete_brand($id){
        $brand = Brand::find($id);
        if(File::exists(public_path('uploads/brands/'.$brand->image))){
            File::delete(public_path('uploads/brands/'.$brand->image));
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('success','Brand has been deleted successfully');
    }

    public function Categories(){
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view('admin.categories',compact('categories'));
    }

    public function add_categories(){
        return view('admin.add-categories');
    }

    public function store_categories(Request $request){
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:categories,slug',
            'image'=>'mimes:png,jpeg,jpg|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imgExtn = $image->extension();
            $imgName = Carbon::now()->timestamp.'.'.$imgExtn;
            $this->GenerateCategoryThumbNailsImage($image,$imgName);
            $category->image = $imgName;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('success','Category has been added successfully');
    }

    public function GenerateCategoryThumbNailsImage($image,$imageName){
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    } 

    public function edit_category($id){
        $category = Category::find($id);
        return view('admin.edit-category',compact('category'));
    }

    public function update_category($id, Request $request){
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:categories,slug,'.$request->id,
            'image'=>'mimes:png,jpeg,jpg|max:2048',
        ]);

        $category = Category::find($id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/categories/'.$category->image))){
                File::delete(public_path('uploads/categories/'.$category->image));
            }
            $image = $request->file('image');
            $imgExtn = $image->extension();
            $imgName = Carbon::now()->timestamp.'.'.$imgExtn;
            $this->GenerateCategoryThumbNailsImage($image,$imgName);
            $category->image = $imgName;
        }
        
        $category->save();
        return redirect()->route('admin.categories')->with('success','Category has been updated successfully');
    }

    public function delete_category($id){
        $category = Category::find($id);
        if(File::exists(public_path('uploads/categories'.$category->image))){
            File::delete(public_path('uploads/categories'.$category->image));
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('success','Category has been deleted successfully');
    }

    public function products(){
        $products = Product::orderBy('created_at','DESC')->paginate(10);
        return view('admin.products',compact('products'));
    }

    public function add_product(){
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
       
        return view('admin.product-add',compact('categories','brands'));
    }

    public function store_product(Request $request){
        $request->validate([
        'name' => 'required',
        'slug'=> 'required|unique:products,slug',
        'short_description'=> 'required',
        'description'=> 'required',
        'regular_price'=> 'required',
        'sale_price'=> 'required',
        'SKU'=> 'required',
        'stock_status'=> 'required',
        'featured'=> 'required',
        'quantity'=> 'required',
        'image'=> 'required|image|mimes:png,jpg,jpeg|max:2048',
        'category_id'=> 'required',
        'brand_id'=> 'required'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbNailsImage($image,$imageName);
            $product->image = $imageName;
        }

        $arr_images = [];
        $counter = 1;
        $gallery_images = '';

        if($request->hasFile('images')){
            $files = $request->file('images');
            $extensions = ['png','jpg','jpeg'];
            foreach($files as $file){
                $file_ext = $file->getClientOriginalExtension();
                $check = in_array($file_ext,$extensions);
                if($check){
                    $gfilename = $current_timestamp."-".$counter.".".$file_ext;
                    $this->GenerateProductThumbNailsImage($file,$gfilename);
                    array_push($arr_images,$gfilename);
                    $counter = $counter+1;
                }
            }
            $gallery_images = implode(',',$arr_images);
        }
        $product->images = $gallery_images;
        $product->save();
        return redirect()->route('admin.products')->with("success","Product has been added successfully!");

    }

    public function GenerateProductThumbNailsImage($image,$imageName){
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products/');
        $img = Image::read($image->path());
        $img->cover(540,689,"top");
        $img->resize(540,689,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

        $img->resize(104,104,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail.'/'.$imageName);
    }

    public function edit_product($id,Request $request){
        $product = Product::find($id);
        $categories = Category::select('id','name')->get();
        $brands = Brand::select('id','name')->get();
        return view('admin.edit-product',compact('product','categories','brands'));
    }

    public function update_product(Request $request){
        $request->validate([
        'name' => 'required',
        'slug'=> 'required|unique:products,slug,'.$request->id,
        'short_description'=> 'required',
        'description'=> 'required',
        'regular_price'=> 'required',
        'sale_price'=> 'required',
        'SKU'=> 'required',
        'stock_status'=> 'required',
        'featured'=> 'required',
        'quantity'=> 'required',
        'image'=> 'mimes:png,jpg,jpeg,|max:2048',
        'category_id'=> 'required',
        'brand_id'=> 'required'
        ]);

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/products/'.$request->image))){
                File::delete(public_path('uploads/products/'.$request->image));
            }
            if(File::exists(public_path('uploads/products/thumbnails/'.$request->image))){
                File::delete(public_path('uploads/products/thumbnails/'.$request->image));
            }
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbNailsImage($image,$imageName);
            $product->image = $imageName;
        }

        $arr_images = [];
        $counter = 1;
        $gallery_images = '';

        if($request->hasFile('images')){
            foreach(explode(',',$product->images) as $fimg){
                if(File::exists(public_path('uploads/products/'.$fimg))){
                    File::delete(public_path('uploads/products/'.$fimg));
                }
                if(File::exists(public_path('uploads/products/thumbnails/'.$fimg))){
                    File::delete(public_path('uploads/products/thumbnails/'.$fimg));
                }
            }
            $files = $request->file('images');
            $extensions = ['png','jpg','jpeg'];
            foreach($files as $file){
                $file_ext = $file->getClientOriginalExtension();
                $check = in_array($file_ext,$extensions);
                if($check){
                    $gfilename = $current_timestamp."-".$counter.".".$file_ext;
                    $this->GenerateProductThumbNailsImage($file,$gfilename);
                    array_push($arr_images,$gfilename);
                    $counter = $counter+1;
                }
            }
            $gallery_images = implode(',',$arr_images);
            $product->images = $gallery_images;
        }
        
        $product->save();
        return redirect()->route('admin.products')->with("success","Product has been updated successfully!");

    }

    public function delete_product($id){
        $product = Product::find($id);
        if(File::exists(public_path('uploads/products/'.$product->image))){
            File::delete(public_path('uploads/products/'.$product->image));
        }
        if(File::exists(public_path('uploads/products/thumbnails'.$product->image))){
            File::delete(public_path('uploads/products/'.$product->image));
        }
        foreach(explode(',',$product->images) as $fimg){
            if(File::exists(public_path('uploads/products/'.$fimg))){
                File::delete(public_path('uploads/products/'.$fimg));
            }
            if(File::exists(public_path('uploads/products/thumbnails/'.$fimg))){
                File::delete(public_path('uploads/products/thumbnails/'.$fimg));
            }
        }
        $product->delete();
        return redirect()->route('admin.products')->with('success','Product has been deleted successfully!');
    }

    public function coupon() {
        $coupons = Coupon::orderBy('expiry_date','DESC')->paginate(10);
        return view('admin.coupons',compact('coupons')); 
    }

    public function add_coupon() {
        return view('admin.add-coupon'); 
    }

    public function store_coupon(Request $request) {
        $request->validate([
            'code'=>'required',
            'type'=>'required',
            'value'=>'required',
            'cart_value'=>'required',
            'expiry_date'=>'required'
            
        ]);
        return view('admin.add-coupon'); 
    }
}
