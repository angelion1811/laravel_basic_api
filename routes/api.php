<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::group(['prefix'=> 'auth'], function ($api) {
	$api->post('/register', 'AuthController@register');
	$api->post('/login', 'AuthController@login');
});

Route::group(['middleware'=>['jwt.verify']], function ($api) {
  Route::group(['prefix'=> 'users'], function ($api) {
    $api->get('/me', 'UsersController@me')->name('users.me');
  });

  Route::group(['prefix'=> 'products'], function ($api) {
    $api->get('/', 'ProductsController@index');
    $api->get('/{id}', 'ProductsController@show');
    $api->post('/', 'ProductsController@store');
    $api->put('/{id}', 'ProductsController@update');
    $api->delete('/{id}', 'ProductsController@destroy');

  });
});
