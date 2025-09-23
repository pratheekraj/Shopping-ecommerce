<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
