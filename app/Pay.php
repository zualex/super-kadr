<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

use Carbon\Carbon;
use App\Pay;


class Pay extends Model {

	public $error;
	
	public function __construct(){
		$this->error = array();
	}
	
	/*
	* Создание заказа
	*	'gallery_id' 
	*	'tarif' 
	*	'monitor'
	*	'dateShow'
	*/
	public function createPay($param){
		$pay = false;
		if(!array_key_exists('gallery_id', $param)){$param['gallery_id'] = '';}
		if(!array_key_exists('tarif', $param)){$param['tarif'] = '';}
		if(!array_key_exists('monitor', $param)){$param['monitor'] = '';}
		if(!array_key_exists('dateShow', $param)){$param['dateShow'] = '';}
		
		
		if($param['gallery_id'] == ''){$this->error[] = 'Нет идентификатора галлереи';}
		if($param['tarif'] == ''){$this->error[] = 'Не выбран тариф';}
		if($param['monitor'] == ''){$this->error[] = 'Не выбран экран';}
		if($param['dateShow'] == ''){$this->error[] = 'Не выбрана дата и начало паказа';}
		if(!Auth::check()){$this->error[] = 'Необходимо авторизоваться';}
		
		
		if(count($this->error) == 0){
			$status_pay = Status::where('type_status', '=', 'pay')->where('caption', '=', 'wait')->first();
			$tarif = Tarif::find($param['tarif']);
						
			if(count($status_pay) == 0){$this->error[] = 'Не найден статус Pay';}
			if(count($tarif) == 0){$this->error[] = 'Не найден тариф';}
		}
		
		if(count($this->error) == 0){
			$pay = new Pay;
			$pay->gallery_id = $param['gallery_id'];
			$pay->status_pay = $status_pay->id;
			$pay->name = "Пользователь: ".Auth::user()->id." сделал заказ";
			$pay->price = $tarif->price;
			$pay->date_show = Carbon::createFromFormat('H:i d.m.Y', $param['dateShow']);
			$pay->tarif_id = $param['tarif'];
			$pay->monitor_id = $param['monitor'];
			$pay->save();
		}

		return $pay;
	}		
		
}
