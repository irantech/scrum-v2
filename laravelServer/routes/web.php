<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('login', 'AuthController@index')->name('login');
//Route::post('login', 'AuthController@store');
//Route::post('do_login', 'BaseController@do_login');

//Route::get('/logout','AuthController@logout');

//Route::get('login', 'BaseController@login');
/*Route::get('/',function(){
    view('layout');
});*/
//Route::get('login', 'BaseController@login')->name('login');
//Route::get('/', '');

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('{any}', 'BaseController@index')->where('any','.*');
