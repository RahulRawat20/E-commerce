<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\GetAccessToken;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    //
    public function index(){
        return view('admin.index');
    }

    # Brands #
        public function brands(){
            $brands = Brand::orderBy('id','desc')->paginate(10);
            return view('admin.brands', compact('brands'));
        }

    # Brands add #
        public function add_brand(){
            return view('admin.brand-add');
        }

    # Brands add post #
        public function brand_store(Request $request)
        {
            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:brands,slug',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'  
            ]);

            $brands = new Brand();
            $brands->name = $request->name;
            $brands->slug = Str::slug($request->slug);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filname = time() . '.'.$extension;
                $file->move('uploads/brands', $filname);
                $brands->Image = $filname;
            }
        
            $brands->save(); 
            return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully!');
        }

    # Brands edit #
        public function brand_edit($id){
            $brand = Brand::find($id);
            return view('admin.brand-edit', compact('brand'));

        }
    # Brands update #
        public function brand_update(Request $request){
            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:brands,slug,'.$request->id,
                //'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'  
            ]);

            $brand = Brand::find($request->id);
            $brand->name = $request->name;
            $brand->slug = Str::slug($request->slug);

            if ($request->hasFile('image')) 
            {
                if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
                    File::delete(public_path('uploads/brands').'/'.$brand->image);
                }
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filname = time() . '.'.$extension;
                $file->move('uploads/brands', $filname);
                $brand->Image = $filname;
            }

                $brand->save();
                return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully
                ');
        }

    # Brands delete #
        public function brand_delete($id){
            $brand = Brand :: find($id);
            if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
                File::delete(public_path('uploads/brands').'/'.$brand->image);
            }
            $brand->delete();
            return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully
            ');
        }

    # categeory #
        public function category(){
            $categories = Category::orderBy('id','DESC')->paginate(10);
            return view('admin.categories', compact('categories'));
        }

    # Category create #
        public function category_add(Request $request){
            return view('admin.catgeory-add');

        }

    # Category store #
        public function category_store(Request $request){ 
            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:categories,slug',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'  
            ]);

            $Category = new Category();
            $Category->name = $request->name;
            $Category->slug = Str::slug($request->slug);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filname = time() . '.'.$extension;
                $file->move('uploads/categories', $filname);
                $Category->Image = $filname;
            }
        
            $Category->save();  
            return redirect()->route('admin.categories')->with('status', 'category has been added successfully!');
        }

    # Category edit #
        public function category_edit($id){
            $category = Category::find($id);
            return view('admin.category-edit', compact('category'));
        }

    # category update #
        public function category_update(Request $request){

            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:categories,slug',
                //'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'  
            ]);

            $Category = Category::find($request->id);
            $Category->name = $request->name;
            $Category->slug = Str::slug($request->slug);

            if ($request->hasFile('image')) 
            {
                if(File::exists(public_path('uploads/categories').'/'.$Category->image)){
                    File::delete(public_path('uploads/categories').'/'.$Category->image);
                }
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filname = time() . '.'.$extension;
                $file->move('uploads/categories', $filname);
                $Category->Image = $filname;
            }

                $Category->save();
                return redirect()->route('admin.categories')->with('status', 'category has been updated successfully
                ');
        }

    # delete category #
        public function category_delete($id){
            $category = category :: find($id);
            if(File::exists(public_path('uploads/categories').'/'.$category->image)){
                File::delete(public_path('uploads/categories').'/'.$category->image);
            }
            $category->delete();
            return redirect()->route('admin.categories')->with('status', 'category has been deleted successfully!');
        }
    #-----------------------------------------------------#
    # Get access token  #
        public function GetRefreshtoken_to_AccessToken(){

            $redirect_uri = env('GOOGLE_REDIRECT');
            $client_id = env('GOOGLE_CLIENT_ID');
            $client_secret = env('GOOGLE_CLIENT_SECRET');
            $refresh_token = env('GOOGLE_REFRESH_TOKEN');
            
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://oauth2.googleapis.com/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query([
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'refresh_token' => $refresh_token,
                    'grant_type' => 'refresh_token',
                ]),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            $response_data = json_decode($response, true);

    # Check if we got the access token
        if (isset($response_data['access_token'])) {
            $access_token = $response_data['access_token'];
            $record = GetAccessToken::first();
                if ($record) {
                    $record->update([
                        'accesstoken' => $access_token,
                    ]);
                } else {
                    GetAccessToken::create([
                        'accesstoken' => $access_token,
                        'refreshtoken' => 1223,
                    ]);
                }
                return response()->json($response_data);
        } else {
            dd($response_data); 
        }
            
        }

    # product #
        public function products(){
            $products = Product::orderby('created_at','DESC')->paginate(10);
            return view('admin.products', compact('products'));
        }

    # product add #
        public function product_add(){
            $category  = category::select('id','name')->orderBy('name')->get();
            $brand = Brand::select('id','name')->orderBy('name')->get();
            return view('admin.product-add', compact('category','brand'));
        }

    # product store in database #
        public function product_store(Request $request)
        {
            //dd($request->all());
            // Validation
            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:products,slug',
                'category_id' => 'required',
                'short_description' => 'required',
                'description' => 'required',
                'regular_price' => 'required',
                'sale_price' => 'required',
                'SKU' => 'required',
                'stock_status' => 'required',
                'featured' => 'required',
                'quantity' => 'required', 
                'image' => 'required|image', 
                'brand_id' => 'required',
            ]);

            // Create a new product instance
            $products = new Product();
            $products->name = $request->name;
            $products->slug = $request->slug;
            $products->short_description = $request->short_description;
            $products->description = $request->description;
            $products->regular_price = $request->regular_price;
            $products->sale_price = $request->sale_price;
            $products->SKU = $request->SKU;
            $products->stock_status = $request->stock_status;
            $products->featured = $request->featured;
            $products->quantity = $request->quantity;
            $products->category_id = $request->category_id;
            $products->brand_id = $request->brand_id;

            // Handle image upload (optional)
            $current_timestamp = Carbon::now()->timestamp;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $current_timestamp . '.' . $image->extension();
                $this->GenerateProductThumbnailImage($image, $imageName);
                $products->image = $imageName;
            }

            $gallery_arr = array();
            $gallery_images = "";

            $counter = 1;

            if ($request->hasFile('images')) {
                $allowedfileExtion = ['jpg', 'png', 'jpeg'];
                $files = $request->file('images');

                foreach ($files as $file) {
                    $gextension = $file->getClientOriginalExtension();
                    $gcheck = in_array($gextension, $allowedfileExtion);

                    if ($gcheck) {
                        $gfileName = $current_timestamp . "_" . $counter . "." . $gextension;
                        $this->GenerateProductThumbnailImage($file, $gfileName);
                        array_push($gallery_arr, $gfileName);
                    }
                    $counter = $counter + 1;
                }

                $gallery_images = implode(",", $gallery_arr);
            }

            $products->images = $gallery_images;

            // Save the product to the database
        // $products->save();
            
            if ($products->save()) {
                $this->sendToGoogleSheets($products);

                return redirect()->route('admin.products')->with('status', 'Product has been added successfully');
            } else {
            
                return redirect()->back()->with('error', 'Failed to save product. Please try again.');
            }

            // Redirect with success message
            return redirect()->route('admin.products')->with('status', 'Product has been added successfully');
        }

        public function GenerateProductThumbnailImage($image, $imageName)
        {
            
            $destinationPathThumbnail = public_path('uploads/products/thumbnails');
            $destinationPath = public_path('uploads/products');
            $img = Image::read($image->path());
            $img->cover(540, 689, "top");

            $img->resize(540, 689, function($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$imageName);

            $img->resize(104, 104, function($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPathThumbnail.'/'.$imageName);
        }

    # Send product data to Google Sheets via API.
        public function sendToGoogleSheets($product)
        {
            $accessToken = GetAccessToken::value('accesstoken');

            
                
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://sheets.googleapis.com/v4/spreadsheets/1NS7yCfD8Kqt1mIYBdf5RqrDykMiXGjoZu9gBWc48sKo/values/Sheet1!A1:append?valueInputOption=RAW',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                "range": "Sheet1!A1",
                "majorDimension": "ROWS",
                "values": [
                    [
                    "'.$product->name.'",
                    "'.$product->slug.'",
                    "'.$product->short_description.'",
                    "'.$product->description.'",
                    "'.$product->regular_price.'",
                    "'.$product->sale_price.'",
                    "'.$product->SKU.'",
                    "'.$product->stock_status.'",
                    "'.$product->quantity.'",
                    "'.$product->category_id.'",
                    "'.$product->brand_id.'"
                    ]
                ]
                }',
                CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $accessToken",
                'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl); 
            curl_close($curl);
            // echo $response; 
            $responseData = json_decode($response, true);
            
            if (isset($responseData['error'])) {
                if (isset($responseData['error']['status']) && $responseData['error']['status'] == "UNAUTHENTICATED") {
                    $newAccessToken = $this->GetRefreshtoken_to_AccessToken();  // Refresh token
                    if ($newAccessToken) {
                        // Retry your API request with the new token
                        $this->sendToGoogleSheets($product);
                    } 
                }
            } else {
                // Handle case where there's no error
                return response()->json(['error' => 'No error information found in response.']);
            }
            
        }
    # product edit #
    public function product_edit($id){
        $product = Product::find($id);
        $category  = category::select('id','name')->orderBy('name')->get();
        $brand = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product','category','brand'));

    }

    # product update #
        public function product_update(Request $request){

            // Validation
            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:products,slug,'.$request->id,
                'category_id' => 'required',
                'short_description' => 'required',
                'description' => 'required',
                'regular_price' => 'required',
                'sale_price' => 'required',
                'SKU' => 'required',
                'stock_status' => 'required',
                'featured' => 'required',
                'quantity' => 'required', 
                'image' => 'required|image', 
                'brand_id' => 'required',
            ]);

            $products = Product::find($request->id);
            $products->name = $request->name;
            $products->slug = $request->slug;
            $products->short_description = $request->short_description;
            $products->description = $request->description;
            $products->regular_price = $request->regular_price;
            $products->sale_price = $request->sale_price;
            $products->SKU = $request->SKU;
            $products->stock_status = $request->stock_status;
            $products->featured = $request->featured;
            $products->quantity = $request->quantity;
            $products->category_id = $request->category_id;
            $products->brand_id = $request->brand_id;

            // Handle image upload (optional)
            $current_timestamp = Carbon::now()->timestamp;

            if ($request->hasFile('image')) {

                if(File::exists(public_path('uploads/products').'/'.$products->image)){
                    File::delete(public_path('uploads/products').'/'.$products->image); 
                }
                if(File::exists(public_path('uploads/products/thumbnails').'/'.$products->image)){
                    File::delete(public_path('uploads/products/thumbnails').'/'.$products->image); 
                }

                $image = $request->file('image');
                $imageName = $current_timestamp . '.' . $image->extension();
                $this->GenerateProductThumbnailImage($image, $imageName);
                $products->image = $imageName;
            }

            $gallery_arr = array();
            $gallery_images = "";
            $counter = 1;

            if ($request->hasFile('images')) {

                foreach(explode(',',$products->images) as $old_file){
                    if(File::exists(public_path('uploads/products').'/'.$old_file)){
                        File::delete(public_path('uploads/products').'/'.$old_file); 
                    }
                    if(File::exists(public_path('uploads/products/thumbnails').'/'.$old_file)){
                        File::delete(public_path('uploads/products/thumbnails').'/'.$old_file); 
                    }

                }
                $allowedfileExtion = ['jpg', 'png', 'jpeg'];
                $files = $request->file('images');

                foreach ($files as $file) {
                    $gextension = $file->getClientOriginalExtension();
                    $gcheck = in_array($gextension, $allowedfileExtion);

                    if ($gcheck) {
                        $gfileName = $current_timestamp . "_" . $counter . "." . $gextension;
                        $this->GenerateProductThumbnailImage($file, $gfileName);
                        array_push($gallery_arr, $gfileName);
                    }
                    $counter = $counter + 1;
                }

                $gallery_images = implode(",", $gallery_arr);
                $products->images = $gallery_images;
            }

            $products->save();
            return redirect()->route('admin.products')->with('status', 'Product has been updated successfully');
        }

    # products delete  #
        public function product_delete($id){
            $products = Product::find($id);

            if(File::exists(public_path('uploads/products').'/'.$products->image)){
                File::delete(public_path('uploads/products').'/'.$products->image); 
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$products->image)){
                File::delete(public_path('uploads/products/thumbnails').'/'.$products->image); 
            }

            foreach(explode(',',$products->images) as $old_file){
                if(File::exists(public_path('uploads/products').'/'.$old_file)){
                    File::delete(public_path('uploads/products').'/'.$old_file); 
                }
                if(File::exists(public_path('uploads/products/thumbnails').'/'.$old_file)){
                    File::delete(public_path('uploads/products/thumbnails').'/'.$old_file); 
                }
            }
            $products->delete();
            return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully');
        
        }

    #   Get coupons 
        Public function coupons(){
            $coupons = Coupon::orderBy('expiry_date', 'desc')->paginate(12);
            return view('admin.coupons',compact('coupons'));
        }

    # add coopon
        public function coupon_add(){
            return view('admin.add-coupon');
        }
    #   save coupon
        public function coupon_store(Request $request){
            $request->validate([
                'code' => 'required',
                'type' => 'required',
                'value' => 'required|numeric',
                'cart_value' => 'required|numeric',
                'expiry_date' => 'required|date',
            ]);

            $coupon = new Coupon();
            $coupon->code = $request->code;
            $coupon->type = $request->type;
            $coupon->value = $request->value;
            $coupon->cart_value = $request->cart_value;
            $coupon->expiry_date = $request->expiry_date;

            if ($coupon->save()) {
                return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully');
            } else {
                 return redirect()->route('admin.coupon.add')->with('status','Failed to save coupon');
            }
        }

        public function coupon_edit($id){
            $coupon = Coupon::find($id);
            return view('admin.coupon-edit', compact('coupon')); 

        }

    # update coupon data store
        public function coupon_update(Request $request){
            $request->validate([
                'code' => 'required',
                'type' => 'required',
                'value' => 'required|numeric',
                'cart_value' => 'required|numeric',
                'expiry_date' => 'required|date',
            ]);
            
            $coupon = Coupon::find($request->id);
            $coupon->code = $request->code;
            $coupon->type = $request->type;
            $coupon->value = $request->value;
            $coupon->cart_value = $request->cart_value;
            $coupon->expiry_date = $request->expiry_date;
            $coupon->save();
            return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated
            successfully');

        }
    # delete coupon #
        public function coupon_delete($id){
            $coupon = Coupon::find($id);
            $coupon->delete();
            return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted
            successfully');
        }



   








}
