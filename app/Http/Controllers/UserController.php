<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        return view('user.index');
    }

    public function orders(){
        $orders = order::where('user_id',Auth::user()->id)->orderBy('created_at','DESC')->paginate(10);
        return view('user.orders',compact('orders'));

    }


    public function order_details($order_id){

        $order = order::where('user_id',Auth::user()->id)->where('id',$order_id)->first();

        if($order){
            $orderItems = OrderItem::where('order_id',$order_id)->orderBy('id')->paginate(12);
            $transactions = Transaction::where('order_id', $order_id)->first();
            return view('user.order_details',compact('order','orderItems','transactions'));
        }else{
            return redirect()->route('login');
        }

    }








}
