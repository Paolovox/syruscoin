<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);


Route::post("get-wallet-balance","UserController@getWalletBalance");


Route::get("/test","UserController@test");


Route::post("/login","UserController@login");
Route::post("/register","UserController@register");
Route::post("/get-coin-qty","UserController@getCoinQty");
Route::post("/send-to","UserController@sentTo");
