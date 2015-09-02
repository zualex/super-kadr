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


Route::get('/', ['as' => 'main', 'uses' => 'HomeController@index']);

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);


//Route::get('login/twitter', 'Auth\AuthController@login');
//Route::get('login/github', 'Auth\AuthController@login');


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

