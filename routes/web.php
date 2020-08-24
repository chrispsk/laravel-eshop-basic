<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', 'ProductsController@laView1'); #->middleware('auth')
Route::get('addtocart/{id}', ['uses'=>'ProductsController@addtocart', 'as' => 'adding']);
#delete item from cart
Route::get('deleteItem/{id}', ['uses'=>'ProductsController@deleteItem', 'as' => 'deleteItem']);

//increase single product in cart
Route::get('product/increase/{id}', ['uses'=>'ProductsController@increaseProduct', 'as' => 'increase']);
//decrease single product in cart
Route::get('product/decrease/{id}', ['uses'=>'ProductsController@decreaseProduct', 'as' => 'decrease']);

//show cart items
Route::get('cart', ['uses'=>'ProductsController@showCart', 'as' => 'cartproducts']);

//Checkout Products
Route::get('products/search', ['uses'=>'ProductsController@search', 'as' => 'search']);

//checkout
Route::get('products/checkoutProducts/', ['uses'=>'ProductsController@checkoutProducts', 'as' => 'checkoutProducts']);

//create order
Route::get('products/createOrder/', ['uses'=>'ProductsController@createOrder', 'as' => 'createOrder']);

//create new order
Route::post('products/createNewOrder/', ['uses'=>'ProductsController@createNewOrder', 'as' => 'createNewOrder']);

//show payment page
Route::get('products/showPaymentPage/', ['uses'=>'ProductsController@showPaymentPage', 'as' => 'showPaymentPage']);

//receipt route
Route::get('payment/paymentreceipt/{paymentID}/{payerID}', ['uses'=>'ProductsController@showPaymentReceipt', 'as' => 'showPaymentReceipt']);

Auth::routes(['verify' => true]);

// Don't let them in dashboard (home) until email is verified
Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');

Route::get('products/men', ['uses'=>'ProductsController@men', 'as' => 'men']);
Route::get('products/women', ['uses'=>'ProductsController@women', 'as' => 'women']);

// protect path in group
// restrictToAdmin is the middleware created to restrict access except admin true (1 or 2 etc) no 0
Route::group(['middleware'=>['restrictToAdmin']], function() {

    //Admin Pannel
    Route::get('/admin/products', 'Admin\AdminProductsController@listing'); //->middleware('restrictToAdmin') or //->middleware('auth')

    Route::get('/admin/insert', 'Admin\AdminProductsController@insert');
    Route::post('/admin/adding', 'Admin\AdminProductsController@adding');

    //display edit product form
    Route::get('admin/editProductForm/{id}', ["uses"=>"Admin\AdminProductsController@editProductForm", "as"=> "adminEditProductForm"]);

    //update product
    Route::post('admin/updateProduct/{id}', ["uses"=>"Admin\AdminProductsController@updateProduct"]);

    //display edit product image form
    Route::get('admin/editProductImageForm/{id}', ["uses"=>"Admin\AdminProductsController@editProductImageForm", "as"=> "adminEditProductImageForm"]);

    //update productImage
    Route::post('admin/updateProductImage/{id}', ["uses"=>"Admin\AdminProductsController@updateProductImage"]);

    //delete product
    Route::get('admin/deleteProduct/{id}', ["uses"=>"Admin\AdminProductsController@deleteProduct", "as"=>"adminDeleteProduct"]);

    //orders panel
    Route::get('admin/ordersPanel', ["uses"=>"Admin\AdminProductsController@ordersPanel", "as"=>"ordersPanel"]);

    //edit order form
    Route::get('admin/adminEditOrderForm/{order_id}', ["uses"=>"Admin\AdminProductsController@adminEditOrderForm", "as"=>"adminEditOrderForm"]);

    //delete order
    Route::get('admin/adminDeleteOrder/{id}', ["uses"=>"Admin\AdminProductsController@adminDeleteOrder", "as"=>"adminDeleteOrder"]);

    //payment/getPaymentInfoByOrderId
    Route::get('payment/getPaymentInfoByOrderId/{order_id}', ["uses"=>"Admin\AdminProductsController@getPaymentInfoByOrderId", "as"=>"getPaymentInfoByOrderId"]);

    //update order data
    Route::post('admin/updateOrder/{order_id}', ["uses"=>"Admin\AdminProductsController@updateOrder", "as"=> "adminUpdateOrder"]);

    //test ajax
    Route::get('admin/ajaxform', ["uses"=>"Admin\AdminProductsController@ajaxform", "as"=> "ajaxform"]);

    //post ajax
    Route::post('admin/ajaxpost', ["uses"=>"Admin\AdminProductsController@ajaxpost", "as"=> "ajaxpost"]);
});
