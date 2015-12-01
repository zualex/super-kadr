<?php namespace App;
use Illuminate\Database\Eloquent\Model;
use Schema;
class Setting extends Model {
	
	/* 
	* Глобальные настройки 
	*Подключаются \app\Providers\AppServiceProvider.php
	*/
	public function getSettingGlobal(){
		$res = array();
		if(Schema::hasTable('settings')){
			$res = $this
				->where('name', '=', 'title')
				->orWhere('name', '=', 'description')
				->orWhere('name', '=', 'keywords')
				->orWhere('name', '=', 'off_site')
				->orWhere('name', '=', 'authorization')
				->orWhere('name', '=', 'send_emails')
				->get();
		}
		return $res;
	}
	
	
	/* 
	* Получение email для уведомления о входе в админку
	*/
	public function getSendEmails(){
		$res = array();
		if(Schema::hasTable('settings')){
			$res = $this->where('name', '=', 'send_emails')->first();
		}
		return $res;
	}
	
	
	/*Основные настройки */
	public function getSettingMain(){
		$setting = $this->where('type', '=', 'main')->get();
		return $setting;
	}
	
	/*Настройки платежей */
	public function getSettingPay(){
		$setting = $this->where('type', '=', 'pay')->get();
		return $setting;
	}
	
	/*настройки пользователей*/
	public function getSettingUser(){
		$res = array();
		if(Schema::hasTable('settings')){
			$res = $this->where('type', '=', 'user')->get();
		}
		return $res;
	}
	
	
	/* Сохранение настроек */
	public function saveSetting($param){
		/* Обнуляем все Checkbox, так как 0 в param не попадает */
		$settingCheckbox = $this->where('type_input', '=', 'checkbox')->get();
		if(count($settingCheckbox) > 0){
			foreach($settingCheckbox as $key => $value){
				$value->value = 0;
				$value->save();
			}
		}
	
		foreach ($param as $key => $value){
			$setting = $this->where('name', '=', $key)->first();
			if(count($setting) > 0){
				$setting->value = $value;
				$setting->save();
			}
		}
		return 1;
	}
	
	
	/* Переменна выключен сайт или нет */
	public function siteOff(){
		$res = false;
		if(Schema::hasTable('settings')){
			$setting = $this->where('name', '=', 'off_site')->first();
			if(count($setting) > 0){$res = $setting->value;}
		}
		return $res;
	}
	
	/* Переменна Включить оплату */
	public function getPayment(){
		$setting = $this->where('name', '=', 'payment')->first();
		return $setting->value;
	}
	
	/* Переменна Платежи Логин */
	public function getPaymentLogin(){
		$setting = $this->where('name', '=', 'payment_login')->first();
		return $setting->value;
	}
	
	/* Переменна Платежи пароль 1 */
	public function getPaymentPassword1(){
		$setting = $this->where('name', '=', 'payment_password_1')->first();
		return $setting->value;
	}
	
	/* Переменна Платежи пароль 2 */
	public function getPaymentPassword2(){
		$setting = $this->where('name', '=', 'payment_password_2')->first();
		return $setting->value;
	}
	
	/* Переменна Тестовые платежи */
	public function getPaymentTest(){
		$setting = $this->where('name', '=', 'payment_test')->first();
		return $setting->value;
	}
	
	
	
	
	
	
	/* 
	*	Создание настройки
	* 	name
	* 	caption
	* 	value
	* 	type
	* 	type_input - необязательное поле
	*/
	public function createSetting($param){
		$setting = new Setting;
		$setting->name = $param['name'];
		$setting->caption = $param['caption'];
		$setting->value = $param['value'];
		$setting->type = $param['type'];
		if(array_key_exists('type_input', $param)){$setting->type_input = $param['type_input'];}
		$setting->save();
	}
	
	/* Создание настроек по умолчанию */
	public function createSettingDefault(){
		/* Основные */
		$this->createSetting(array(
			'name' => 'title',
			'caption' => 'Название сайта',
			'value' => 'Супер Кадр',
			'type' => 'main',
		));
		$this->createSetting(array(
			'name' => 'description',
			'caption' => 'Описание (Description)',
			'value' => '',
			'type' => 'main',
		));
		$this->createSetting(array(
			'name' => 'keywords',
			'caption' => 'Ключевые слова',
			'value' => '',
			'type' => 'main',
		));
		$this->createSetting(array(
			'name' => 'off_site',
			'caption' => 'Выключить сайт',
			'value' => '0',
			'type' => 'main',
			'type_input' => 'checkbox',
		));
		$this->createSetting(array(
			'name' => 'send_emails',
			'caption' => 'Уведомление о входе',
			'value' => '',
			'type' => 'main',
		));
		
		
		/* Платежи */
		$this->createSetting(array(
			'name' => 'payment',
			'caption' => 'Включить оплату',
			'value' => '0',
			'type' => 'pay',
			'type_input' => 'checkbox',
		));
		$this->createSetting(array(
			'name' => 'payment_login',
			'caption' => 'Логин',
			'value' => 'super-kadr32.test',
			'type' => 'pay',
		));
		$this->createSetting(array(
			'name' => 'payment_password_1',
			'caption' => 'Пароль 1',
			'value' => 'rj754Rd4PK',
			'type' => 'pay',
		));
		$this->createSetting(array(
			'name' => 'payment_password_2',
			'caption' => 'Пароль 2',
			'value' => 'rj754Rd4PK234234',
			'type' => 'pay',
		));
		$this->createSetting(array(
			'name' => 'payment_test',
			'caption' => 'Тестовые платежи',
			'value' => '1',
			'type' => 'pay',
			'type_input' => 'checkbox',
		));
		
		
		/* Пользователи */
		$this->createSetting(array(
			'name' => 'authorization',
			'caption' => 'Включить авторизацию',
			'value' => '1',
			'type' => 'user',
			'type_input' => 'checkbox',
		));
		$this->createSetting(array(
			'name' => 'twitter_id',
			'caption' => 'Идентификатор Twitter',
			'value' => 't3O4iFG6Dr9EIl54Rld5POdHp',
			'type' => 'user',
		));
		$this->createSetting(array(
			'name' => 'twitter_secret',
			'caption' => 'Секретный ключ Twitter',
			'value' => 'WNKnYcJMGmq5tB0Kqtj5YO4w3a6tfyjWAUo4pMZd7sUi1AXQUA',
			'type' => 'user',
		));
		
		$this->createSetting(array(
			'name' => 'facebook_id',
			'caption' => 'Идентификатор Facebook',
			'value' => '1690710344477613',
			'type' => 'user',
		));
		$this->createSetting(array(
			'name' => 'facebook_secret',
			'caption' => 'Секретный ключ Facebook',
			'value' => '7d0922387e821f652e3283902d1a0c0c',
			'type' => 'user',
		));
		
		$this->createSetting(array(
			'name' => 'vk_id',
			'caption' => 'Идентификатор VK',
			'value' => '5055126',
			'type' => 'user',
		));
		$this->createSetting(array(
			'name' => 'vk_secret',
			'caption' => 'Секретный ключ VK',
			'value' => 'K26XMIpmnE0pVEk2WbnQ',
			'type' => 'user',
		));
		
		$this->createSetting(array(
			'name' => 'od_id',
			'caption' => 'Идентификатор Odnoklassniki',
			'value' => '1111',
			'type' => 'user',
		));
		$this->createSetting(array(
			'name' => 'od_secret',
			'caption' => 'Секретный ключ Odnoklassniki',
			'value' => '1111',
			'type' => 'user',
		));
		$this->createSetting(array(
			'name' => 'od_public',
			'caption' => 'Публичный ключ Odnoklassniki',
			'value' => '1111',
			'type' => 'user',
		));
		
		
	}
}