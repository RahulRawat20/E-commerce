<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
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







}
