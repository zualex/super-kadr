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


Route::get('/pay/conditions/{gallery_id}', ['as' => 'pay.conditions', 'middleware' => 'auth', 'uses' => 'PayController@conditions']);		//Принятие условий перед оплатой
Route::get('/pay/index/{gallery_id}', ['as' => 'pay.index', 'middleware' => 'auth', 'uses' => 'PayController@index']);			//Отправка данных для оплаты
Route::get('/pay/result', ['as' => 'pay.result', 'middleware' => 'auth', 'uses' => 'PayController@result']);			//Result Url
Route::get('/pay/success', ['as' => 'pay.success', 'middleware' => 'auth', 'uses' => 'PayController@success']);	//Success Url
Route::get('/pay/fail', ['as' => 'pay.fail', 'middleware' => 'auth', 'uses' => 'PayController@fail']);						//Fail Url



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

	Route::get('/', ['as' => 'admin', 'uses' => 'Admin\AdminController@index']);
	
	/* Смена пароля */
	Route::get('/change_password', ['as' => 'change_password', 'uses' => 'Admin\UserController@change_password']);
	Route::post('/change_password/save', ['as' => 'change_password.save', 'uses' => 'Admin\UserController@change_password_save']);
	
	
	/* Заказы */
	Route::get('/gallery/', ['as' => 'admin.gallery.index', 'uses' => 'Admin\AdminGalleryController@index']);
	Route::get('/gallery/success/{id}', ['as' => 'admin.gallery.success', 'uses' => 'Admin\AdminGalleryController@success']);
	Route::get('/gallery/cancel/{id}', ['as' => 'admin.gallery.cancel', 'uses' => 'Admin\AdminGalleryController@cancel']);
	Route::get('/gallery/delete/{id}', ['as' => 'admin.gallery.delete', 'uses' => 'Admin\AdminGalleryController@delete']);
	
	Route::post('/gallery/success/all/', ['as' => 'admin.gallery.success_all', 'uses' => 'Admin\AdminGalleryController@successAll']);
	Route::post('/gallery/moderation/all/', ['as' => 'admin.gallery.moderation_all', 'uses' => 'Admin\AdminGalleryController@moderationAll']);
	Route::post('/gallery/delete/all/', ['as' => 'admin.gallery.delete_all', 'uses' => 'Admin\AdminGalleryController@deleteAll']);
	

	
	
	/* Тарифы */
	/*Route::resource('/tarif', 'Admin\AdminTarifController', array('names' => array(
		'index' => 'admin.tarif.index',
		'create' => 'admin.tarif.create',
		'store' => 'admin.tarif.store',
		'show' => 'admin.tarif.show',
		'edit' => 'admin.tarif.edit',
		'update' => 'admin.tarif.update',
		'destroy' => 'admin.tarif.destroy',
	)));*/
	
	/* Транзакции */
	Route::resource('/pay', 'Admin\AdminPayController', array('names' => array(
		'index' => 'admin.pay.index',
		'create' => 'admin.pay.create',
		'store' => 'admin.pay.store',
		'show' => 'admin.pay.show',
		'edit' => 'admin.pay.edit',
		'update' => 'admin.pay.update',
		'destroy' => 'admin.pay.destroy',
	)));
	
	/* Настройки */
	Route::resource('/playlist', 'Admin\AdminPlaylistController', array('names' => array(
		'index' => 'admin.playlist.index',
		'create' => 'admin.playlist.create',
		'store' => 'admin.playlist.store',
		'show' => 'admin.playlist.show',
		'edit' => 'admin.playlist.edit',
		'update' => 'admin.playlist.update',
		'destroy' => 'admin.playlist.destroy',
	)));
	
	/* Настройки */
	Route::resource('/setting', 'Admin\AdminSettingController', array('names' => array(
		'index' => 'admin.setting.index',
		'create' => 'admin.setting.create',
		'store' => 'admin.setting.store',
		'show' => 'admin.setting.show',
		'edit' => 'admin.setting.edit',
		'update' => 'admin.setting.update',
		'destroy' => 'admin.setting.destroy',
	)));
	
	/* Пользователи */
	/*Route::resource('/users', 'Admin\UserController', array('names' => array(
		'index' => 'admin.users.index',
		'create' => 'admin.users.create',
		'store' => 'admin.users.store',
		'show' => 'admin.users.show',
		'edit' => 'admin.users.edit',
		'update' => 'admin.users.update',
		'destroy' => 'admin.users.destroy',
	)));*/
});

