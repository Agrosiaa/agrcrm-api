<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});
$app->group(['middleware' => ['cors']], function ($app) {
    $app->get('/customer-orders', array('uses' => 'OrderController@customerOrders'));
    $app->get('/csr-orders', array('uses' => 'OrderController@csrOrders'));
    $app->get('/get-post-office-info/{id}', array('uses' => 'AuthController@getPostOfficeInfo'));
    $app->get('/get-pincode',array('uses' => 'AuthController@getPincode'));
    $app->get('/get-products',array('uses' => 'ProductController@getProducts'));
    $app->get('/order-search', array('uses' => 'OrderController@orderSearch'));
    $app->get('/order-detail', array('uses' => 'OrderController@orderDetails'));
    $app->get('/order-chat', array('uses' => 'OrderController@orderChats'));
    $app->post('/order-reply', array('uses' => 'OrderController@orderReply'));
    $app->post('/order-cancel', array('uses' => 'OrderController@orderCancel'));
    $app->get('/get-customers',array('uses' => 'CustomerController@getCustomers'));
    $app->get('/customer-profile', array('uses' => 'CustomerController@customerProfile'));
    $app->post('/create-customer', array('uses' => 'CustomerController@createCustomer'));
    $app->get('/created-customers', array('uses' => 'CustomerController@createdCustomers'));
    $app->post('/edit-profile',array('uses' => 'CustomerController@editProfile'));
    $app->post('/add-address',array('uses' => 'CustomerController@addAddress'));
    $app->get('/delete-address',array('uses' => 'CustomerController@deleteAddress'));
    $app->post('/edit-address',array('uses' => 'CustomerController@editAddress'));
    $app->get('/get-abandoned-cart-data',array('uses' => 'CustomerController@abandonedListing'));
    $app->get('/get-abandoned-carts',array('uses' => 'CustomerController@abandonedCarts'));
    $app->get('/abandoned-cart-details/{id}',array('uses' => 'CustomerController@abandonedDetails'));
    $app->post('/generate-order',array('uses' => 'OrderController@generate'));
    $app->get('/report-data',array('uses' => 'ReportController@reportData'));
});

