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
// Route::get('/chatroom/discover', function(){return view('login.chatroom.discover');}) -> name('login.chatroom.discover');

// logout pages
Route::get('/', 'AuthController@home') -> name('logout.home');
Route::get('/login', 'AuthController@index') -> name('logout.login');
Route::get('/register', 'AuthController@register') -> name('logout.register');
Route::get('/forgotPassword', 'AuthController@forgotPassword') -> name('logout.forgotPassword');
Route::get('/resetPassword', 'AuthController@resetPassword') -> name('logout.resetPassword');

Route::post('/ajaxLogin', 'AuthController@ajaxLogin') -> name('ajax.login');
Route::post('/ajaxRegister', 'AuthController@ajaxRegister') -> name('ajax.register');

// login pages
Route::get('/home', 'PageController@index') -> name('login.home');
Route::get('/chatroom/discover', 'UserController@discoverUser') -> name('login.chatroom.discover');
Route::get('/chatroom/addContact/{unique_id}', 'ContactController@addContact') -> name('login.chatroom.addContact');
Route::get('/chatroom/startChat/{unique_id}', 'UserController@discoverUser') -> name('login.chatroom.startChat');

Route::get('/chatroom/addContact', 'ContactController@index') -> name('login.chatroom.contacts');

Route::get('/login/account/profile', 'PageController@profile') -> name('login.account.profile');
Route::get('/login/account/editPassword', 'PageController@editPassword') -> name('login.account.editPassword');
Route::get('/login/account/logout', 'AuthController@logout') -> name('login.account.logout');