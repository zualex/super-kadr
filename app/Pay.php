<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

use Carbon\Carbon;
use App\Pay;
use App\Gallery;
use App\Tarif;
use App\Monitor;


class Pay extends Model {

	public $error;
	
	public function __construct(){
		$this->error = array();
	}
	
	
	public function gallery()
    {
        return $this->belongsTo('App\Gallery');
    }
	
	
	
	
	public function getAll(){
		$pay = false;
		$pay = $this
			->where('visible', '=', '1')
			->orderBy('created_at')
			->get();
		//dd($pay);
		return $pay;
	}
	
	/*
	* Создание заказа
	*	'gallery_id' 
	*	'tarif' 
	*/
	public function createPay($param){
		$pay = false;
		if(!array_key_exists('gallery_id', $param)){$param['gallery_id'] = '';}
		if(!array_key_exists('tarif', $param)){$param['tarif'] = '';}
		
		if($param['gallery_id'] == ''){$this->error[] = 'Нет идентификатора галлереи';}
		if($param['tarif'] == ''){$this->error[] = 'Не выбран тариф';}
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
			$pay->save();
		}

		return $pay;
	}		
		
}
