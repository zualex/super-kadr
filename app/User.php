<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Auth;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;


	protected $table = 'users';
	protected $fillable = ['name', 'email', 'password', 'provider','social_id', 'nickname', 'avatar'];
	protected $hidden = ['password', 'remember_token'];
	
	public $errors;
	
	public function __construct(){
		$this->errors = array();
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
	*		'nickname' 
	*		'name' 
	*		'email' 
	*		'avatar' 
	*		'level' 
	*/
	public function CreateOrGetUser($arrValues){
		$userAuth = false;
		if(!array_key_exists('provider', $arrValues)){$arrValues['provider'] = '';}
		if(!array_key_exists('social_id', $arrValues)){$arrValues['social_id'] = '';}
		if(!array_key_exists('level', $arrValues)){$arrValues['level'] = 'user';}
		
		//if(!$arrValues['email']){$this->errors[] = 'email: ' . $arrValues['email'] . ' уже используется';}
		
		
		
		//Social Auth
		if($arrValues['provider'] != ''){
			//Проверка что email свободен
			$checkUser = $this
				->where('provider', '<>', $arrValues['provider'])
				->where('social_id', '<>', $arrValues['social_id'])
				->where('email', '=', $arrValues['email'])
				->get();
				
			if(count($checkUser) == 0){
				$checkUserSocial = $this
					->where('provider', '=', $arrValues['provider'])
					->where('social_id', '=', $arrValues['social_id'])
					->first();
				if(count($checkUserSocial) == 0){
					//Создаем пользователя
					$userAuth = new User;
					$userAuth->provider = $arrValues['provider'];
					$userAuth->social_id = $arrValues['social_id'];
					$userAuth->nickname = $arrValues['nickname'];
					$userAuth->name = $arrValues['name'];
					$userAuth->email = $arrValues['email'];
					$userAuth->avatar = $arrValues['avatar'];
					$userAuth->level = $arrValues['level'];
					$userAuth->save();
				}else{
					//Обновляем пользователя
					$userAuth = $checkUserSocial;
					$userAuth->nickname = $arrValues['nickname'];
					$userAuth->name = $arrValues['name'];
					$userAuth->email = $arrValues['email'];
					$userAuth->avatar = $arrValues['avatar'];
					$userAuth->level = $arrValues['level'];
					$userAuth->save();
				}
				
				
				/*$userAuth = $this->firstOrCreate([
					'provider' => $arrValues['provider'],
					'social_id' => $arrValues['social_id'],
					'nickname' => $arrValues['nickname'],
					'name' => $arrValues['name'],
					'email' => $arrValues['email'],
					'avatar' => $arrValues['avatar'],
					'level' => $arrValues['level'],
				]);*/

			}else{
				$this->errors[] = 'email: ' . $arrValues['email'] . ' уже используется';
			}
		}
	
		
		return $userAuth;
	}

}
