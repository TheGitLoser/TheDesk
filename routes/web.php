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


// logout pages
Route::get('/', function () { return view('logout.home'); }) -> name('logout.home');
Route::get('login', 'LogoutController@index') -> name('logout.login');
Route::get('register', function () { return view('logout.register'); }) -> name('logout.register');
Route::get('forgotPassword', function () { return view('logout.forgotPassword'); }) -> name('logout.forgotPassword');
Route::get('resetPassword', 'LogoutController@resetPassword') -> name('logout.resetPassword');


Route::get('/home', function () { return view('login.home'); }) -> name('home');