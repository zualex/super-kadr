<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Route;
use View;
use Request;
use Config;
use App\Setting;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot(Setting $settingModel)
	{
		/*
		* во все шаблоны включаются Основные настройки
		*/
		$result = array();
		$setting = $settingModel->getSettingMain();
		if(count($setting) > 0){
			foreach($setting as $key => $value){
				$result[$value->name] = $value->value;
			}
		}

		View::share ('mainSetting', $result);
		
		
		$social = array();
		$settingSocial = $settingModel->getSettingUser();
		if(count($settingSocial) > 0){
			foreach($settingSocial as $key => $value){
				$social[$value->name] = $value->value;
			}
		}
		
		
		Config::set('services.twitter.client_id', $social['twitter_id']);
		Config::set('services.twitter.client_secret', $social['twitter_secret']);
		Config::set('services.twitter.redirect', Request::root().'/login/callback/twitter');
		
		Config::set('services.facebook.client_id', $social['facebook_id']);
		Config::set('services.facebook.client_secret', $social['facebook_secret']);
		Config::set('services.facebook.redirect', Request::root().'/login/callback/facebook');
		
		Config::set('services.vkontakte.client_id', $social['vk_id']);
		Config::set('services.vkontakte.client_secret', $social['vk_secret']);
		Config::set('services.vkontakte.redirect', Request::root().'/login/callback/vkontakte');
		
		Config::set('services.odnoklassniki.client_id', $social['od_id']);
		Config::set('services.odnoklassniki.client_secret', $social['od_secret']);
		Config::set('services.odnoklassniki.redirect', Request::root().'/login/callback/odnoklassniki');

		
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'App\Services\Registrar'
		);
	}

}
