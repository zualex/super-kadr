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



/*
* Pages
*/
Route::get('/', ['as' => 'main', 'uses' => 'HomeController@index']);
Route::get('/gallery', ['as' => 'gallery', 'uses' => 'GalleryController@index']);
Route::get('/gallery/{id}', ['as' => 'gallery.show', 'uses' => 'GalleryController@show']);
Route::get('/gallery_create', ['as' => 'gallery.create', 'uses' => 'GalleryController@create']);





/*
* Auth admin
*/
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);



/*
* Social Auth
*/
Route::get('/login/{provider?}',[
    'uses' => 'Auth\AuthController@getSocialAuth',
    'as'   => 'auth.getSocialAuth'
]);
Route::get('/login/callback/{provider?}',[
    'uses' => 'Auth\AuthController@getSocialAuthCallback',
    'as'   => 'auth.getSocialAuthCallback'
]);



/*
* Admin panel
*/
Route::group(['prefix' => 'admin', 'middleware' => 'authAdmin'], function(){

	Route::get('/', ['as' => 'admin', 'uses' => 'AdminController@index']);
	
	Route::resource('/users', 'UserController', array('names' => array(
		'index' => 'admin.users.index',
		'create' => 'admin.users.create',
		'store' => 'admin.users.store',
		'show' => 'admin.users.show',
		'edit' => 'admin.users.edit',
		'update' => 'admin.users.update',
		'destroy' => 'admin.users.destroy',
	)));
});

