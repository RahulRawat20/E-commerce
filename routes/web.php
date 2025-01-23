<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Cartcontroller;
use App\Http\Controllers\Shopcontroller;
use App\Http\Controllers\WishlistController;
//use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthAdmin;


Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
//shop page //
route::get('/shop',[Shopcontroller::class,'index'])->name('shop.index');
route::get('/shop/{product_slug}',[Shopcontroller::class,'product_details'])->name('shop.products.details');

// shoping cart //
route::get('/cart',[Cartcontroller::class,'index'])->name('cart.index');
route::post('/cart/add',[Cartcontroller::class,'add_to_cart'])->name('cart.add');
route::put('/cart/increase-quantity/{rowId}',[Cartcontroller::class,'increase_cart_quantity'])->name('cart.qty.increase');
route::put('/cart/decrease-quantity/{rowId}',[Cartcontroller::class,'decrease_cart_quantity'])->name('cart.qty.decrease');
route::delete('/cart/remove-cart/{rowId}',[Cartcontroller::class,'remove_cart'])->name('cart.item.remove');
route::delete('/cart/clear',[Cartcontroller::class,'empty_cart'])->name('cart.empty');

// coupon aplied on cart //
route::post('/cart/apply-coupon',[Cartcontroller::class,'apply_coupon_code'])->name('cart.coupon.apply');
route::delete('/cart/remove-coupon',[Cartcontroller::class,'remove_coupon_code'])->name('cart.coupon.remove');


// whishlist //
route::post('/whishlist/add',[WishlistController::class,'add_to_wishlist'])->name('wishlist.add');
route::get('/wishlist',[WishlistController::class,'index'])->name('wishlist.index');
route::DELETE('/wishlist/item/remove/{rowId}',[WishlistController::class,'remove_wishlist'])->name('wishlist.item.remove');
route::DELETE('/wishlist/clear',[WishlistController::class,'empty_wishlist'])->name('wishlist.items.clear');

route::post('/wishlist/move-to-cart/{rowId}',[WishlistController::class,'move_to_cart'])->name('wishlist.move.to.cart');

 
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

    #--product--#
    route::get('/admin/products',[AdminController::class,'products'])->name('admin.products');
    route::get('/admin/product/add',[AdminController::class,'product_add'])->name ('admin.product.add');
    route::POST('admin/product/store',[AdminController::class,'product_store'])->name ('admin.product.store');

    route::get('/admin/product/{id}/edit',[AdminController::class,'product_edit'])->name('admin.product.edit');
    route::PUT('/admin/product/update',[AdminController::class,'product_update'])->name('admin.product.update');
    route::DELETE('/admin/product/{id}/delete',[AdminController::class,'product_delete'])->name('admin.product.delete');

    #--Google api--#
    Route::get('/get-refresh-token', [AdminController::class, 'GetRefreshtoken_to_AccessToken']);

    #---Get coupons ---#
    route::get('/admin/coupons',[AdminController::class,'coupons'])->name('admin.coupons');
    route::get('/admin/coupon/add',[AdminController::class,'coupon_add'])->name('admin.coupon.add');
    route::POST('/admin/coupon/store',[AdminController::class,'coupon_store'])->name('admin.coupon.store');

    route::get('/admin/coupon/{id}/edit',[AdminController::class,'coupon_edit'])->name('admin.coupon.edit');
    route::put('/admin/coupon/update',[AdminController::class,'coupon_update'])->name('admin.coupon.update');
    
    route::DELETE('/admin/coupon/{id}/delete',[AdminController::class,'coupon_delete'])->name('admin.coupon.delete');
    
    


    
});