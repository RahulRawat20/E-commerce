<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
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
        public function apply_coupon_code(Request $request){

            $coupon_code = $request->coupon_code;
            if(isset($coupon_code)){

                //$coupon = Coupon :: where('code',$coupon_code)->where('expiry_date','>=',Carbon::today())->where('cart_value','<=',Cart::instance('cart')->subtotal())->first();
                $coupon = Coupon::where('code', $coupon_code)
                ->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
                ->first();
                if(!$coupon){
                    return redirect()->back()->with('error', 'Please provide a valid coupon code.');
                }else{
                    session::put('coupon',[
                        'code'=>$coupon->code,
                        'type'=>$coupon->type,
                        'value'=>$coupon->value,
                        'cart_value'=>$coupon->cart_value
                ]);
                $this->calculatediscount();
                return redirect()->back()->with('success','Coupon Code Applied Successfully');
                }
            }else{
                return redirect()->back()->with('error', 'Please provide a valid coupon code.');
            }
        }

    # calculate discout coupon function
        public function calculatediscount(){
            $discount = 0;
            if(session::has('coupon')){
                if(session::get('coupon')['type'] == 'fixed'){
                    $discount = session::get('coupon')['value'];
                }else{
                    $discount = (Cart::instance('cart')->subtotal() * session::get('coupon')['value'])/100;
                }
                $subtotal_after_discount = Cart::instance('cart')->subtotal() - $discount;
                $tax_afetr_discout = ($subtotal_after_discount * config('cart.tax'))/100;
                $total_after_discount = $subtotal_after_discount + $tax_afetr_discout;

                session::put('discount',[
                    'discount'=> number_format(floatval($discount),2,'.',''),
                    'subtotal'=> number_format(floatval($subtotal_after_discount),2,'.',''),
                    'tax'=> number_format(floatval($tax_afetr_discout),2,'.',''),
                    'total'=> number_format(floatval($total_after_discount),2,'.','')
                ]);
            }
        }

        # remove coupon code
        public function remove_coupon_code(){
            session:: forget('coupon');
            session:: forget('discount');
            return back()->with('success','Coupon has been removed!!');
        }










}
