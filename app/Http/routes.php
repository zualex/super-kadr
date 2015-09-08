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
Route::get('/', ['as' => 'main', 'uses' => 'HomeController@index']);									//Главная страница
Route::get('/gallery', ['as' => 'gallery', 'uses' => 'GalleryController@index']);					//Страница галереи
Route::get('/gallery/{id}', ['as' => 'gallery.show', 'uses' => 'GalleryController@show']);	//Детальная страница галереи
Route::get('/conditions', ['as' => 'conditions', function(){ return view('pages.conditions.index'); }]);	//Страница Услуги
Route::get('/contacts', ['as' => 'contacts', function(){ return view('pages.contacts.index'); }]);			//Страница Контакты


/*
* Actions
*/
Route::post('/croppic_upload', ['as' => 'gallery.upload', 'uses' => 'GalleryController@upload']);		//Загрузка изображения croppic
Route::post('/gallery_create', ['as' => 'gallery.create', 'uses' => 'GalleryController@create']);		//Создание галереи
Route::post('/gallery_like', ['as' => 'gallery.like', 'uses' => 'GalleryController@like']);					//Лайк

Route::get('/comment/{gallery_id}', ['as' => 'comment.index', 'uses' => 'CommentController@index']);				//Вывод комментариев
Route::post('/comment/save', ['as' => 'comment.save', 'uses' => 'CommentController@save']);					//Сохранение комментария



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
	
	/* Смена пароля */
	Route::get('/change_password', ['as' => 'change_password', 'uses' => 'UserController@change_password']);
	Route::post('/change_password/save', ['as' => 'change_password.save', 'uses' => 'UserController@change_password_save']);
	
	/* Пользователи */
	
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

