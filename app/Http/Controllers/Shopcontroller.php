<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class Shopcontroller extends Controller
{
    //

    #-- Shop page--#
    public Function index (){
        $products = Product::orderby('created_at','DESC')->paginate(12);
        return view('shop ',compact('products'));
    }

    #-- Product details --#  
    public Function product_details ($product_slug){
        $product = Product::where('slug', $product_slug)->first();
        $products = product::where('slug','<>', $product_slug)->get()->take(8);
        return view('details ',compact('product','products'));
    }  



}
