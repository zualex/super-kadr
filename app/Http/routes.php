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
Route::get('/', ['as' => 'main', 'uses' => 'HomeController@index']);														//Главная страница
Route::get('/gallery', ['as' => 'gallery', 'uses' => 'GalleryController@index']);										//Страница галереи
Route::get('/gallery/{id}', ['as' => 'gallery.show', 'uses' => 'GalleryController@show']);						//Детальная страница галереи
Route::get('/competition', ['as' => 'competition', 'uses' => 'CompetitionController@index']);					//страница галереи конкурсов
Route::get('/competition/{id}', ['as' => 'competition.show', 'uses' => 'CompetitionController@show']);	//страница пользователя и его галереи
Route::get('/conditions', ['as' => 'conditions', function(){ return view('pages.conditions.index'); }]);	//Страница Услуги
Route::get('/contacts', ['as' => 'contacts', function(){ return view('pages.contacts.index'); }]);			//Страница Контакты
Route::get('/dev', ['as' => 'dev', function(){ return view('dev'); }]);	//Станица при отключении сайта
/*
* Actions
*/
Route::post('/croppic_upload', ['as' => 'gallery.upload', 'uses' => 'GalleryController@upload']);		//Загрузка изображения croppic
Route::post('/gallery_create', ['as' => 'gallery.create', 'uses' => 'GalleryController@create']);		//Создание галереи
Route::post('/gallery_like', ['as' => 'gallery.like', 'uses' => 'GalleryController@like']);					//Лайк
Route::get('/comment/{gallery_id}', ['as' => 'comment.index', 'uses' => 'CommentController@index']);				//Вывод комментариев
Route::post('/comment/save', ['as' => 'comment.save', 'uses' => 'CommentController@save']);					//Сохранение комментария
Route::get('/pay/conditions/{gallery_id}', ['as' => 'pay.conditions', 'uses' => 'PayController@conditions']);		//Принятие условий перед оплатой
Route::get('/pay/index/{gallery_id}', ['as' => 'pay.index','uses' => 'PayController@index']);			//Отправка данных для оплаты
Route::get('/pay/result', ['as' => 'pay.result', 'uses' => 'PayController@result']);			//Result Url
Route::get('/pay/success', ['as' => 'pay.success', 'uses' => 'PayController@success']);	//Success Url
Route::get('/pay/fail', ['as' => 'pay.fail','uses' => 'PayController@fail']);						//Fail Url
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
	Route::get('/change_password', ['as' => 'change_password', 'uses' => 'Admin\AdminUserController@change_password']);
	Route::post('/change_password/save', ['as' => 'change_password.save', 'uses' => 'Admin\AdminUserController@change_password_save']);
	
	
	
	/* Заказы */
	Route::get('/gallery/', ['as' => 'admin.gallery.index', 'uses' => 'Admin\AdminGalleryController@index']);
	Route::get('/gallery/application', ['as' => 'admin.gallery.application', 'uses' => 'Admin\AdminGalleryController@application']);
	Route::get('/gallery/success/{id}', ['as' => 'admin.gallery.success', 'uses' => 'Admin\AdminGalleryController@success']);
	Route::get('/gallery/cancel/{id}', ['as' => 'admin.gallery.cancel', 'uses' => 'Admin\AdminGalleryController@cancel']);
	Route::get('/gallery/delete/{id}', ['as' => 'admin.gallery.delete', 'uses' => 'Admin\AdminGalleryController@delete']);
	Route::post('/gallery/like/{id}', ['as' => 'admin.gallery.like', 'uses' => 'Admin\AdminGalleryController@like']);
	
	Route::post('/gallery/success/all/', ['as' => 'admin.gallery.success_all', 'uses' => 'Admin\AdminGalleryController@successAll']);
	Route::post('/gallery/moderation/all/', ['as' => 'admin.gallery.moderation_all', 'uses' => 'Admin\AdminGalleryController@moderationAll']);
	Route::post('/gallery/delete/all/', ['as' => 'admin.gallery.delete_all', 'uses' => 'Admin\AdminGalleryController@deleteAll']);
	
	
	/* Конкурс */
	Route::get('/competition/', ['as' => 'admin.competition.index', 'uses' => 'Admin\AdminCompetition@index']);
	
	Route::post('/competition/save/', ['as' => 'admin.competition.save', 'uses' => 'Admin\AdminCompetition@save']);
	Route::post('/competition/save_extra/', ['as' => 'admin.competition.save_extra', 'uses' => 'Admin\AdminCompetition@saveExtra']);
	Route::get('/competition/delete/', ['as' => 'admin.competition.delete', 'uses' => 'Admin\AdminCompetition@delete']);
	
	
	/* Транзакции */
	Route::get('/pay/', ['middleware' => 'checkAdmin', 'as' => 'admin.pay.index', 'uses' => 'Admin\AdminPayController@index']);
	Route::get('/pay/hide/{id}', ['middleware' => 'checkAdmin', 'as' => 'admin.pay.hide', 'uses' => 'Admin\AdminPayController@hide']);
	
	Route::post('/pay/paid/all/', ['middleware' => 'checkAdmin', 'as' => 'admin.pay.paid_all', 'uses' => 'Admin\AdminPayController@paidAll']);
	Route::post('/pay/wait/all/', ['middleware' => 'checkAdmin', 'as' => 'admin.pay.wait_all', 'uses' => 'Admin\AdminPayController@waitAll']);
	Route::post('/pay/cancel/all/', ['middleware' => 'checkAdmin', 'as' => 'admin.pay.cancel_all', 'uses' => 'Admin\AdminPayController@cancelAll']);
	Route::post('/pay/hide/all/', ['middleware' => 'checkAdmin', 'as' => 'admin.pay.hide_all', 'uses' => 'Admin\AdminPayController@hideAll']);
	
	/* Плейлисты */
	Route::get('/playlist/', ['as' => 'admin.playlist.index', 'uses' => 'Admin\AdminPlaylistController@index']);
	Route::get('/playlist/delete/{id}', ['as' => 'admin.playlist.delete', 'uses' => 'Admin\AdminPlaylistController@delete']);
	Route::post('/playlist/enable/{id}', ['as' => 'admin.playlist.enable', 'uses' => 'Admin\AdminPlaylistController@enable']);
	Route::post('/playlist/isTime/{id}', ['as' => 'admin.playlist.isTime', 'uses' => 'Admin\AdminPlaylistController@isTime']);
	Route::post('/playlist/saveExtraVideo/', ['as' => 'admin.playlist.saveExtraVideo', 'uses' => 'Admin\AdminPlaylistController@saveExtraVideo']);
	Route::get('/playlist/deleteExtraVideo/{id}', ['as' => 'admin.playlist.deleteExtraVideo', 'uses' => 'Admin\AdminPlaylistController@deleteExtraVideo']);
	
	/* Экраны */
	Route::get('/monitor/', ['middleware' => 'checkAdmin', 'as' => 'admin.monitor.index', 'uses' => 'Admin\AdminMonitorController@index']);
	Route::post('/monitor/success/', ['middleware' => 'checkAdmin', 'as' => 'admin.monitor.success', 'uses' => 'Admin\AdminMonitorController@success']);
	
	
	/* Настройки */
	Route::get('/setting/', ['middleware' => 'checkAdmin', 'as' => 'admin.setting.index', 'uses' => 'Admin\AdminSettingController@index']);
	Route::post('/setting/success/all/', ['middleware' => 'checkAdmin', 'as' => 'admin.setting.success_all', 'uses' => 'Admin\AdminSettingController@successAll']);
	
	/* Пользователи */
	Route::get('/users/', ['middleware' => 'checkAdmin', 'as' => 'admin.users.index', 'uses' => 'Admin\AdminUserController@index']);
	Route::get('/users/edit/{id}', ['middleware' => 'checkAdmin', 'as' => 'admin.users.edit', 'uses' => 'Admin\AdminUserController@edit']);
	Route::get('/users/destroy/{id}', ['middleware' => 'checkAdmin', 'as' => 'admin.users.destroy', 'uses' => 'Admin\AdminUserController@destroy']);
	Route::get('/users/create', ['middleware' => 'checkAdmin', 'as' => 'admin.users.create', 'uses' => 'Admin\AdminUserController@create']);
	
	Route::post('/users/update/{id}', ['middleware' => 'checkAdmin', 'as' => 'admin.users.update', 'uses' => 'Admin\AdminUserController@update']);
	Route::post('/users/store', ['middleware' => 'checkAdmin', 'as' => 'admin.users.store', 'uses' => 'Admin\AdminUserController@store']);
	
	
	/*Route::resource('/users', 'Admin\AdminUserController', array('names' => array(
		'index' => 'admin.users.index',
		'create' => 'admin.users.create',
		'store' => 'admin.users.store',
		'show' => 'admin.users.show',
		'edit' => 'admin.users.edit',
		'update' => 'admin.users.update',
		'destroy' => 'admin.users.destroy',
	)));*/
});
Route::get('/cron/playlist/initfile', ['as' => 'cron.playlist.initfile', 'uses' => 'Admin\AdminPlaylistController@initFile']);				//Загрузка исходных файлов в БД
Route::get('/cron/playlist/initgenerate', ['as' => 'cron.playlist.initgenerate', 'uses' => 'Admin\AdminPlaylistController@initGenerate']);				//Генерация плейлистов
Route::get('/json/checkdate', ['as' => 'playlist.checkdate', 'uses' => 'Admin\AdminPlaylistController@checkdate']);