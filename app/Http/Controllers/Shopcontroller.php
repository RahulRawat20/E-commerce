<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Switch_;

class Shopcontroller extends Controller
{
    //

    #-- Shop page--#
    public Function index (Request $request){
        $size = $request->query('size') ? $request->query('size') : 12; 
        $order_column = "";
        $o_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;

        $f_brands = $request->query('brands');

        Switch($order){
            case 1:
                $order_column = "created_at";
                $o_order = "DESC";
                break;
            case 2:
                $order_column = "created_at";
                $o_order = "ASC";
                break;
            case 3:
                $order_column = "sale_price";
                $o_order = "asc";
                break;
            case 4:
                $order_column = "sale_price";
                $o_order = "desc";
                break;
            default:
                $order_column = "id";
                $o_order = "desc";
                break;
        }

        $brands = Brand::orderBy('name','ASC')->get();

        $products = Product::where(function($query)use($f_brands){
            $query->whereIn('brand_id',explode(',',$f_brands))->orWhereRaw("'".$f_brands."'=''");
        })
        
        ->orderBy($order_column,$o_order)->paginate($size);
        return view('shop ',compact('products','size','order','brands','f_brands'));
    }

    #-- Product details --#  
    public Function product_details ($product_slug){
        $product = Product::where('slug', $product_slug)->first();
        $products = product::where('slug','<>', $product_slug)->get()->take(8);
        return view('details ',compact('product','products'));
    }  



}
