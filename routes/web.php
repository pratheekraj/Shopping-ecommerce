<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'product_details'])->name('shop.product-details');

Route::get('/cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add',[CartController::class,'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowid}',[CartController::class,'increase_cart_quantity'])->name('cart.qty-increase');
Route::put('/cart/decrease-quantity/{rowid}',[CartController::class,'decrease_cart_quantity'])->name('cart.qty-decrease');
Route::delete('/cart/remove/{rowId}',[CartController::class,'remove_item'])->name('cart.item-remove');
Route::delete('/cart/clear',[CartController::class,'empty_cart'])->name('cart.clear');

Route::post('/cart/apply-coupon', [CartController::class, 'apply_couponcode'])->name('cart.coupon-apply');

Route::post('/wishlist/add',[WishlistController::class,'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist',[WishlistController::class,'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}',[WishlistController::class,'remove_item'])->name('wishlist.item-remove');
Route::delete('/wishlist/clear',[WishlistController::class,'empty_wishlist'])->name('wishlist.items-clear');
Route::post('/wishlist/move-to-cart/{rowId}',[WishlistController::class,'move_to_cart'])->name('wishlist.move-to-cart');

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');

});

Route::middleware(['auth',AuthAdmin::class])->group(function(){
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand/add', [AdminController::class, 'add_brand'])->name('admin.add-brand');
    Route::post('/admin/brand/store', [AdminController::class, 'store_brand'])->name('admin.store-brand');
    Route::get('/admin/brand/edit/{id}', [AdminController::class, 'edit_brand'])->name('admin.brand-edit');
    Route::put('/admin/brand/update/{id}', [AdminController::class, 'update_brand'])->name('admin.update-brand');
    Route::delete('/admin/brand/delete/{id}', [AdminController::class, 'delete_brand'])->name('admin.brand-delete');

    Route::get('/admin/categories', [AdminController::class, 'Categories'])->name('admin.categories');
    Route::get('/admin/category/add', [AdminController::class, 'add_categories'])->name('admin.add-category');
    Route::post('/admin/category/store', [AdminController::class, 'store_categories'])->name('admin.store-category');
    Route::get('/admin/category/edit/{id}', [AdminController::class, 'edit_category'])->name('admin.edit-category');
    Route::put('/admin/category/update/{id}', [AdminController::class, 'update_category'])->name('admin.update-category');
    Route::delete('/admin/category/delete/{id}', [AdminController::class, 'delete_category'])->name('admin.delete-category');
    
    Route::get('/admin/products', [AdminController::class,'products'])->name('admin.products');
    Route::get('/admin/product-add', [AdminController::class,'add_product'])->name('admin.product-add');
    Route::post('/admin/product/store', [AdminController::class,'store_product'])->name('admin.store-product');
    Route::get('/admin/product/{id}/edit', [AdminController::class,'edit_product'])->name('admin.edit-product');
    Route::put('/admin/product/update', [AdminController::class,'update_product'])->name('admin.update_product');
    Route::delete('/admin/product/{id}/delete', [AdminController::class,'delete_product'])->name('admin.product-delete');

    Route::get('/admin/coupons', [AdminController::class,'coupon'])->name('admin.coupons');
    Route::get('/admin/add-coupon', [AdminController::class,'add_coupon'])->name('admin.add-coupon');
    Route::post('/admin/coupon/store', [AdminController::class,'store_coupon'])->name('admin.store_coupon');
    Route::get('/admin/coupon/{id}/edit', [AdminController::class,'edit_coupon'])->name('admin.edit_coupon');
    Route::put('/admin/coupon/update', [AdminController::class,'update_coupon'])->name('admin.update_coupon');
    Route::delete('/admin/coupon/{id}/delete', [AdminController::class,'delete_coupon'])->name('admin.delete_coupon');
});