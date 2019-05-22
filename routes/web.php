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
    $app->get('/order-detail', array('uses' => 'AuthController@orderDetails'));
    $app->get('/order-search', array('uses' => 'AuthController@orderSearch'));
    $app->get('/order-chat', array('uses' => 'AuthController@orderChats'));
    $app->post('/order-reply', array('uses' => 'AuthController@orderReply'));
    $app->post('/order-cancel', array('uses' => 'AuthController@orderCancel'));
    $app->post('/create-customer', array('uses' => 'AuthController@createCustomer'));
    $app->get('/created-customers', array('uses' => 'AuthController@createdCustomers'));
    $app->get('/get-post-office-info/{id}', array('uses' => 'AuthController@getPostOfficeInfo'));
    $app->get('/get-pincode',array('uses' => 'AuthController@getPincode'));
});

