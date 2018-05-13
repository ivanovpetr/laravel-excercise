<?php

use Illuminate\Http\Request;

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
});*/

//Products
Route::get('products', 'Api\V1\ProductController@index');
Route::get('product/{id}', 'Api\V1\ProductController@show');
Route::post('product', 'Api\V1\ProductController@store');
Route::put('product', 'Api\V1\ProductController@store');
Route::delete('product/{id}', 'Api\V1\ProductController@destroy');
//Category
Route::get('categories', 'Api\V1\CategoryController@index');
Route::get('category/{id}', 'Api\V1\CategoryController@show');
Route::post('category', 'Api\V1\CategoryController@store');
Route::put('category', 'Api\V1\CategoryController@store');
Route::delete('category/{id}', 'Api\V1\CategoryController@destroy');

