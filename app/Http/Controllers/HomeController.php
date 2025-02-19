<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\contact;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Slide;

class HomeController extends Controller
{
   

   
    public function index()
    {
        $slides = Slide::where('status',1)->get()->take(3);
        $categories = Category::orderBy('name')->get();
        $sproducts = Product::whereNotNull('sale_price')->where('sale_price','<>','')->inRandomOrder()->get()->take(8);
        $fproducts = Product::where('featured',1)->get()->take(8);
        return view('index',compact('slides','categories','sproducts','fproducts'));
    }

    // about_us
    public function about_us(){
        return view('about-us');
    }

    // Contact us
    public function contact(){
        return view('contact');
    }

    public function contact_store(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'phone'=>'required|numeric|digits:10',
            'comment'=>'required',
            ]);
            $contact = new contact();
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->phone = $request->phone;
            $contact->comment = $request->comment;
            $contact->save();
            return redirect()->back()->with('success','Your message has been sent successfully');

    }

    // Searching products
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name', 'like', '%' . $query . '%')->take(8)->get();
        return response()->json($results);
    }





}
