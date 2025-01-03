<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
//use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthAdmin;


Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
 
// user auth
Route::middleware(['auth'])->group(function () {
    route::get('/account-dashboard',[UserController ::class,'index'])->name('user.index');
    
});

// Admin auth
Route::middleware(['auth',AuthAdmin::class])->group(function () {
    #--show admin index--#
    route::get('/admin',[AdminController ::class,'index'])->name('admin.index');
    #--Brands--#
    route::get('/admin/brands',[AdminController::class,'brands'])->name('admin.brands');
    route::get('/admin/brand/add',[AdminController::class,'add_brand'])->name('admin.brand-add');
    route::POST('/admin/brand/store',[AdminController::class,'brand_store'])->name('admin.brand.store');

    route::get('/admin/brand/edit/{id}',[AdminController::class,'brand_edit'])->name('admin.brand.edit');
    route::put('/admin/brand/update',[AdminController::class,'brand_update'])->name('admin.brand.update');
    Route::DELETE('/admin/brand/edit/{id}/delete',[AdminController::class,'brand_delete'])->name('admin.brand.delete');
    
    #--Categories--#
    route::get('/admin/categories',[AdminController::class,'category'])->name('admin.categories');
    route::get('/admin/category/agit config --global user.namedd',[AdminController::class,'category_add'])->name('admin.catgeory.add');
    route::POST('/admin/category/store',[AdminController::class,'category_store'])->name('admin.category.store');

    route::get('/admin/category/{id}/edit',[AdminController::class,'category_edit'])->name('admin.category.edit');
    route::put('/admin/category/update',[AdminController::class,'category_update'])->name('admin.category.update');
    route::DELETE('admin/category/{id}/delete',[AdminController::class,'category_delete'])->name ('admin.category.delete');
    
    


    
});