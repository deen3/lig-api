<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Posts;

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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('/posts/{post}/comments', 'CommentsController@store');
    Route::patch('/posts/{post}/comments/{comment}', 'CommentsController@update');
    Route::delete('/posts/{post}/comments/{comment}', 'CommentsController@destroy');

    Route::post('/posts', 'PostsController@store');
    Route::patch('/posts/{post}', 'PostsController@update');
    Route::delete('/posts/{post}', 'PostsController@destroy');

    Route::post('logout', 'Auth\LoginController@logout');
});

Route::get('/posts/{post}/comments', 'CommentsController@show');
Route::get('/posts', 'PostsController@index');
Route::get('/posts/{post}', 'PostsController@show');

Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');



