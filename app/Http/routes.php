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

Route::get("/test","UserController@test");


Route::group(['middleware' => ['web']], function () {
	Route::post("/api/login","UserController@login");
	Route::post("/api/register","UserController@register");
	Route::post("/api/get-coin-qty","UserController@getCoinQty");
	Route::post("/api/send-to","UserController@sendTo");
	Route::post("/getWallet", "UserController@getWalletAddress");
	Route::post("/api/get-info", "UserController@getInfo");
	Route::post("/api/get-my-transactions", "UserController@getMytransactions");
});


Route::get('/random',"UserController@randomTransactions");
Route::get("/getLastTransactions", "UserController@getLastTransactions");
Route::get("/getCurrentBlock", "UserController@getCurrentBlock");
Route::get("/getCurrentDifficulty", "UserController@getCurrentDifficulty");
Route::get("/countTransactions", "UserController@countTransactions");




//VIEWVS
Route::get('/transaction', "UserController@transaction");
Route::get('/search', "UserController@search");


Route::get('/login', function(){
	return view('pages.login');
});
Route::get('/register', function(){
	return view('pages.register');
});
Route::get('/admin', function(){
	return view('pages.admin');
});
