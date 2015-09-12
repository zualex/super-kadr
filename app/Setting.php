<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {

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
		$setting = $this->where('type', '=', 'user')->get();
		return $setting;
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
			'name' => 'name_site',
			'caption' => 'Название сайта',
			'value' => 'Супер Кадр',
			'type' => 'main',
		));
		$this->createSetting(array(
			'name' => 'description_site',
			'caption' => 'Описание (Description)',
			'value' => '',
			'type' => 'main',
		));
		$this->createSetting(array(
			'name' => 'keywords_site',
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
			'name' => 'id_secret',
			'caption' => 'Секретный ключ Odnoklassniki',
			'value' => '1111',
			'type' => 'user',
		));

	}

}
