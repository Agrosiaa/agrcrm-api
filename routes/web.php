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
    $app->get('/order-chat', array('uses' => 'AuthController@orderChats'));
    $app->post('/order-reply', array('uses' => 'AuthController@orderReply'));
    $app->post('/order-cancel', array('uses' => 'AuthController@orderCancel'));
});

