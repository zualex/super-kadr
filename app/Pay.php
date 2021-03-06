<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

use Carbon\Carbon;
use App\Pay;
use App\Gallery;
use App\Tarif;
use App\Monitor;
use App\Status;
use DB;


class Pay extends Model {

	public $error;
	
	public function __construct(){
		$this->error = array();
	}
	
	
	public function gallery()
    {
        return $this->belongsTo('App\Gallery');
    }
	
	
	
	
	
	public function getAll($dateFrom = '', $dateTo = ''){
		$pay = false;
		
		$nowDate = Carbon::now();
		if($dateFrom == ''){$dateFrom = $nowDate->format('Y-m-d');}
		if($dateTo == ''){$dateTo = $nowDate->format('Y-m-d');}
		$dateTo = Carbon::parse($dateTo)->addDay(1);
		
		$pay = $this
			->select(DB::raw('pays.*, galleries.src, statuses.name as status_name, statuses.caption as status_caption, users.name as user_name, users.provider'))
			->join('galleries', 'galleries.id', '=', 'pays.gallery_id')
			->join('statuses', 'statuses.id', '=', 'pays.status_pay')
			->leftJoin('users', 'users.id', '=', 'galleries.user_id')
			->where('pays.visible', '=', '1')
			->where('pays.created_at', '>=', $dateFrom)
			->where('pays.created_at', '<=', $dateTo)
			->orderBy('pays.created_at', 'desc')
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
