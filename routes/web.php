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

// checkout 
route::get('/checkout',[Cartcontroller::class,'checkout'])->name('cart.checkout');
route::post('/place-an-order',[Cartcontroller::class,'place_an_order'])->name('cart.place.an.order');
route::get('/order-confirmation',[Cartcontroller::class,'order_confirmation'])->name('cart.order.confirmation');

// Contact
route::get('/contact-us',[HomeController::class,'contact'])->name('home.contact');
route::post('/contact/store',[HomeController::class,'contact_store'])->name('home.contact.store');

//About us
route::get('/about-us',[HomeController::class,'about_us'])->name('home.about');

//Search products
route::get('/search',[HomeController::class,'search'])->name('home.search');

 
// user auth
Route::middleware(['auth'])->group(function () {
    route::get('/account-dashboard',[UserController ::class,'index'])->name('user.index');
    route::get('/account-orders',[UserController ::class,'orders'])->name('user.orders');
    route::get('/account-orders/{order_id}/details',[UserController ::class,'order_details'])->name('user.order.details');

    route::put('/account-order/cancel-order',[UserController::class,'order_cancel'])->name('user.order.cancel');
    
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
    
    #---Get orders ---#
    route::get('/admin/orders',[AdminController::class,'orders'])->name('admin.orders');
    route::get('/admin/order/{order_id}/details',[AdminController::class,'order_details'])->name('admin.order.details');
    route::put('/admin/order/update-status',[AdminController::class,'update_order_status'])->name('admin.order.status.update');

    #---Slides---#
    route::get('/admin/slides',[AdminController::class,'slides'])->name('admin.slides');
    route::get('/admin/slide/add',[AdminController::class,'slide_add'])->name('admin.slide.add');
    route::POST('/admin/slide/store',[AdminController::class,'slide_store'])->name('admin.slide.store');

    route::get('/admin/slide/{id}/edit',[AdminController::class,'slide_edit'])->name('admin.slide.edit');
    route::put('/admin/slide/update',[AdminController::class,'slide_update'])->name('admin.slide.update');

    route::delete('/admin/slide/{id}/delete',[AdminController::class,'slide_delete'])->name('admin.slide.delete');

    // contacts
    route::get('/admin/contacts',[AdminController::class,'contacts'])->name('admin.contacts');
    route::delete('/admin/contacts/{id}/delete',[AdminController::class,'contact_delete'])->name('admin.contacts.delete');
    
    // admin search product
    route::get('/admin/search',[AdminController::class,'search'])->name('admin.search');




    
});