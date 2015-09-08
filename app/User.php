<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Auth;
use Hash;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;


	protected $table = 'users';
	protected $fillable = ['name', 'email', 'password', 'provider', 'social_id', 'avatar'];
	protected $hidden = ['password', 'remember_token'];
	
	public $errors;
	public $defaultAvatar;
	
	public function __construct(){
		$this->errors = array();
		$this->defaultAvatar = '/public/img/default-user.jpg';
	}
	
	
	public function checkAdmin(){
		$admin = false;
		if (Auth::check() and Auth::user()->level == 'admin'){$admin = true;}

		return $admin;
	}
	
	
	/*
	* Create user if not
	* Fields:
	*		'provider' 
	*		'social_id' 
	*		'name' 
	*		'email' 
	*		'avatar' 
	*/
	public function socialAuth($arrValues){
		$userId = false;
		if(!array_key_exists('provider', $arrValues)){$arrValues['provider'] = '';}
		if(!array_key_exists('social_id', $arrValues)){$arrValues['social_id'] = '';}
		if(!$arrValues['name']){
			if($arrValues['email']){
				$split = explode("@", $arrValues['email']);
				$arrValues['name'] = $split[0];
			}else{
				$arrValues['name'] = str_random(9);
			}
		}
				
		if($arrValues['provider'] != ''){
		
			$checkUser = $this
				->where('provider', '=', $arrValues['provider'])
				->where('social_id', '=', $arrValues['social_id'])
				->first();
				
			if(count($checkUser) == 0){
				//Создаем пользователя
				$userAuth = new User;
				$userAuth->provider = $arrValues['provider'];
				$userAuth->social_id = $arrValues['social_id'];
				$userAuth->name = $arrValues['name'];
				$userAuth->email = $arrValues['social_id'].'@'.$arrValues['provider'].'.ru';
				$userAuth->avatar = $arrValues['avatar'];
				$userAuth->password = Hash::make('secret');
				$userAuth->save();
				
				$userId  = $userAuth->id;
			}else{
				//Обновляем пользователя
				$userAuth = $checkUser;
				$userAuth->name = $arrValues['name'];
				$userAuth->avatar = $arrValues['avatar'];
				$userAuth->save();
				
				$userId  = $userAuth->id;
			}
		}
	
		return $userId;
	}
	
	
	
	

}
