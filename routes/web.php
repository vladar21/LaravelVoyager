<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/currency', 'CurrencyController@index');
Route::get('nbu', array('as' => 'parametrs',
                                 'uses' => 'CurrencyController@getnbu'));

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
