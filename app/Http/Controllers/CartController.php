<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session as FacadesSession;


use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index(){
        $items = Cart::instance('cart')->content();
        return view('cart',compact('items'));
    }

    public function add_to_cart(Request $request){
      
        Cart::instance('cart')->add($request->id,$request->name,$request->quantity,$request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_cart_quantity($rowid){
        $product = Cart::instance('cart')->get($rowid);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowid,$qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowid){
        $product = Cart::instance('cart')->get($rowid);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowid,$qty);
        return redirect()->back();
    }

    public function remove_item($rowId){
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart(){
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_couponcode(Request $request){
        $coupon_code = $request->coupon->code;
        if(isset($coupon_code)){
            $coupon = Coupon::where('code',$coupon_code)->where('expiry_date','>=',Carbon::today())
            ->where('cart_value','<=',Cart::instance('cart')->subtotal())->first();
            if(!$coupon){
                return redirect()->back()->with('error','Invalid coupon code!');
            }else{
                Session::put('coupon',[
                    'code'=>$coupon->code,
                    'type'=>$coupon->type,
                    'value'=>$coupon->value,
                    'cart_value'=>$coupon->cart_value
                ]);
            }
        }else{
            return redirect()->back()->with('error','Invalid coupon code!');
        }
    }
}
