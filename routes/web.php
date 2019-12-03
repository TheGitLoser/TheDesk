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

Route::get('/test', 'TemplateController@test') -> name('test');
// Route::get('/test/{a}', 'TemplateController@test') -> name('test');

// logout pages
Route::get('/', 'AuthController@home') -> name('logout.home');
Route::get('/login', 'AuthController@index') -> name('logout.login');
Route::get('/register', 'AuthController@register') -> name('logout.register');
Route::get('/forgotPassword', 'AuthController@forgotPassword') -> name('logout.forgotPassword');
Route::get('/resetPassword', 'AuthController@resetPassword') -> name('logout.resetPassword');

Route::post('/ajaxLogin', 'AuthController@ajaxLogin') -> name('ajax.login');
Route::post('/ajaxRegister', 'AuthController@ajaxRegister') -> name('ajax.register');


Route::get('/home', 'PageController@index') -> name('login.home');