<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class Cartcontroller extends Controller
{
    //
    public function index(){
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }
    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
        return redirect()->back();
    }
    
    // cart quantity
    public function increase_cart_quantity($rowId){
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();

    }
    public function decrease_cart_quantity($rowId){
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
        
    }

    // Remove cart
    public function remove_cart($rowId){
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }
    // Remove all cart
    public function empty_cart (){
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    # aplly coupon code functionalty
    public function apply_coupon_code(){

    }

}
