<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
//use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;


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

    # check out page
        public function checkout(){
            if(!Auth::check()){
                return redirect()->route('login');
            }
            $address = Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();
            return view('checkout',compact('address'));
        }
    
    # place order address
        public function place_an_order(Request $request){
            //dd($request);
            $user_id = Auth::user()->id;
  
            $address = Address::where('user_id', $user_id)
                  ->where('isdefault', true)
                  ->first();
            if(!$address){
                
                $address = new Address();
                $address->user_id = $user_id; 
                $address->name = $request->name;
                $address->phone = $request->phone;
                $address->zip = $request->zip;
                $address->address = $request->address;
                $address->city = $request->city;
                $address->state = $request->state;
                $address->country = 'India'; // Hardcoded
                $address->locality = $request->locality;
                $address->landmark = $request->landmark;
                $address->isdefault = true;
                
                $address->save();
                
            }

            $this -> setAmmountForCheckout();

            $order = new Order();

            $order->user_id = $user_id;
            $order->subtotal = Session::get('checkout')['subtotal'];
            $order->discount = Session::get('checkout')['discount'];
            $order->tax = Session::get('checkout')['tax'];
            $order->total = Session::get('checkout')['total'];

            $order->name = $address->name;
            $order->phone = $address->phone;
            $order->locality = $address->locality;
            $order->address = $address->address;
            $order->city = $address->city;
            $order->state = $address->state;
            $order->country = $address->country;
            $order->landmark = $address->landmark;
            $order->zip = $address->zip;

            $order->save();

            // Save order items
            foreach (Cart::instance('cart')->content() as $item) {
                $orderItem = new OrderItem();
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->price = $item->price;
                $orderItem->quantity = $item->qty;
                $orderItem->save();
            }

            if ($request->mode == "card") {
                // Card payment logic
            } elseif ($request->mode == "Paypal") {
                // PayPal payment logic
            } elseif ($request->mode == "Cod") {
                $transaction = new Transaction();
                $transaction->user_id = $user_id;
                $transaction->order_id = $order->id;
                $transaction->mode = $request->mode;
                $transaction->status = "pending";
                $transaction->save();
            }

            Cart::instance('cart')->destroy();
            Session::forget('checkout');
            Session::forget('coupon');
            Session::forget('discount');
            Session::put('order_id',$order->id);
            return redirect()->route('cart.order.confirmation');

        }

    # set ammount for checkout
    public function setAmmountForCheckout() {
        if (!Cart::instance('cart')->content()->count() > 0) {
            session::forget('checkout');
            return;
        }
        if (session::has('coupon')) {
            session::put('checkout', [
                'discount' => floatval(session::get('discount')['discounts']),
                'subtotal' => floatval(session::get('discount')['subtotal']),
                'tax' => floatval(session::get('discount')['tax']),
                'total' => floatval(session::get('discount')['total']),
            ]);
        } else {
            session::put('checkout', [
                'discount' => 0.00,
                'subtotal' => floatval(Cart::instance('cart')->subtotal(2, '.', '')),
                'tax' => floatval(Cart::instance('cart')->tax(2, '.', '')),
                'total' => floatval(Cart::instance('cart')->total(2, '.', '')),
            ]);
        }
    }

        public function order_confirmation(){
            if(session::has('order_id')){
                $order = Order::find(session::get('order_id'));
                return view('order-confirmation',compact('order'));
            }
            return redirect()->route('cart.index');
        }






            











}
