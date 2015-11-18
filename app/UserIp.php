<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use Carbon\Carbon;

class UserIp extends Model {
	
	public $timeBlock;
	public $attempt;
	
	public function __construct(){
		$this->timeBlock = 900; 	//время блокировки
		$this->attempt = 5; 			//Кол-во попыток
	}
	
	
	/*
	* badLogin - увеличение кол-во не удавшихся попыток
	*/
	public function badLogin(){
		$clientIp = Request::getClientIp();
		
		$userIp =$this
			->where('ip_address', '=', $clientIp)
			->first();
			
		if(count($userIp) == 0){$userIp = $this->initUser();}

		$userIp->count += 1;
		$userIp->save();
		
		// Если лимит исчерпан то блокируем
		if($userIp->count >= $this->attempt){
			$this->setTimeBlock($userIp);
		}
		
		return $userIp->count;
	}
	
	
	
	/*
	* initUser - инициализация в базе данных
	*/
	public function initUser(){
		$userIp = new UserIp;
		$userIp->ip_address = Request::getClientIp();
		$userIp->count = 0;
		$userIp->save();
		
		return $userIp;
	}
	
	
	
	/*
	* setTimeBlock - установление даты начала блокировки
	*/
	public function setTimeBlock($userIp){
		$userIp->count = 0;
		$userIp->date_block = Carbon::now();
		$userIp->save();
		
		return Carbon::now();
	}
	
	/*
	* checkBlock - проверка заблокирован пользователь или нет
	*/
	public function checkBlock(){
		$res = 0;
		
		$clientIp = Request::getClientIp();
		$userIp =$this
			->where('ip_address', '=', $clientIp)
			->first();
		if(count($userIp) > 0){			
			$diffSec = Carbon::parse($userIp->date_block)->diffInSeconds(Carbon::now());
			if($diffSec <= $this->timeBlock){$res = 1;}
		}
		
		return $res;
	}
}
