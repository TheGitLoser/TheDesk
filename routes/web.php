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

Route::post('/ajax/Login', 'AuthController@ajaxLogin') -> name('ajax.login');
Route::post('/ajax/Register', 'AuthController@ajaxRegister') -> name('ajax.register');

// login pages
Route::get('/login/account/logout', 'AuthController@logout') -> name('login.account.logout');

// chatroom
Route::get('/chatroom/chat/{unique_id}', 'ChatroomController@chat') -> name('login.chatroom.chat');
Route::get('/chatroom/setting/{unique_id}', 'ChatroomController@setting') -> name('login.chatroom.setting');
Route::post('/ajax/chatroom/setting', 'ChatroomController@ajaxSetting') -> name('ajax.chatroom.setting');

Route::post('/ajax/newMessage', 'ChatroomController@ajaxNewMessage') -> name('ajax.chatroom.newMessage');

Route::get('/chatroom/createChatroom/{unique_id}', 'ChatroomController@addToChat') -> name('login.chatroom.startChat');
Route::post('/chatroom/createChannel', 'ChatroomController@createChannel') -> name('login.chatroom.createChannel');
Route::post('/ajax/createChannel', 'ChatroomController@ajaxCreateChannel') -> name('ajax.createChannel');

// contact list
Route::get('/chatroom/contacts', 'ContactController@index') -> name('login.chatroom.contacts');
Route::post('/ajax/searchContact', 'ContactController@ajaxSearchContact') -> name('ajax.searchContact');
Route::get('/chatroom/addContact/{unique_id}', 'ContactController@addContact') -> name('login.chatroom.addContact');
Route::get('/chatroom/hideContact/{unique_id}', 'ContactController@hideContact') -> name('login.chatroom.hideContact');

// discover user
Route::get('/chatroom/discover', 'UserController@discoverUser') -> name('login.chatroom.discover');
Route::post('/ajax/discover', 'UserController@ajaxDiscover') -> name('ajax.discover');
Route::get('/request/new/{unique_id}', 'ContactController@hideContact') -> name('login.request.new');

// profile management
Route::get('/home', 'PageController@index') -> name('login.home');
Route::get('/login/account/profile', 'UserController@profile') -> name('login.account.profile');
Route::post('/ajax/updateProfile', 'UserController@ajaxUpdateProfile') -> name('ajax.updateProfile');
Route::get('/login/account/editPassword', 'PageController@editPassword') -> name('login.account.editPassword');
Route::post('/ajax/editPassword', 'UserController@ajaxEditPassword') -> name('ajax.editPassword');

// business admin 
Route::get('/businessAdmin/home', 'BusinessAdminController@index') -> name('login.businessAdmin.dashboard');
Route::get('/businessAdmin/user', 'BusinessAdminController@viewUser') -> name('login.businessAdmin.viewUser');
Route::get('/businessAdmin/removeBusinessPlanUser/{unique_id}', 'BusinessAdminController@removeBusinessPlanUser') -> name('login.businessAdmin.removeBusinessPlanUser');
Route::get('/businessAdmin/addUser', 'BusinessAdminController@addUser') -> name('login.businessAdmin.addUser');
Route::post('/ajax/createBusinessUser', 'BusinessAdminController@ajaxCreateBusinessUser') -> name('ajax.createBusinessUser');

// admin
Route::get('/admin/home', 'AdminController@index') -> name('login.admin.dashboard');
Route::get('/admin/businessPlan', 'AdminController@viweBusinessPlan') -> name('login.admin.viewBusinessPlan');
Route::post('/ajax/searchBusinessPlan', 'AdminController@ajaxSearchBusinessPlan') -> name('ajax.searchBusinessPlan');
Route::get('/admin/businessPlanDetails/{unique_id}', 'AdminController@viweBusinessPlanDetails') -> name('login.admin.viewBusinessPlanDetails');

Route::get('/admin/createBusinessPlan', 'AdminController@createBusinessPlan') -> name('login.admin.createBusinessPlan');
Route::post('/ajax/createBusinessPlan', 'AdminController@ajaxCreateBusinessPlan') -> name('ajax.createBusinessPlan');