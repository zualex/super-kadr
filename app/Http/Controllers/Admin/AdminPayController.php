<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

use App\Gallery;
use App\Status;
use App\Pay;
use Session;
use Response;
use Config;
use Carbon\Carbon;

class AdminPayController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Pay $payModel, Gallery $galleryModel)
	{
	
		$nowDate = Carbon::now();
		$dateFrom = $nowDate->format('Y-m-d');		
		$dateTo = $nowDate->format('Y-m-d');
		if (Request::has('dateFrom')){ $dateFrom = Carbon::parse(Request::input('dateFrom'))->format('Y-m-d');}
		if (Request::has('dateTo')){ $dateTo = Carbon::parse(Request::input('dateTo'))->format('Y-m-d');}
		$data = array(
			"dateFrom" => $dateFrom,
			"dateTo" => $dateTo,
		);
	
		$pay = $payModel->getAll($dateFrom, $dateTo);
		
		return view('admin.pay.index')
			->with('pay', $pay)
			->with('pathImages', $galleryModel->pathImages)
			->with('data', $data);
	}

	/*
	* Изменение статуса для одной записи
	*/
	public function changeStatus($id, $status_id)
	{	
        $pay = Pay::find($id);
		$pay->status_pay = $status_id;
		$pay->save();
		
		/* При выставление статуса оплачено устанавливаем значение начала модерации */
		$status_paid= Status::where('type_status', '=', 'pay')->where('caption', '=', 'paid')->first();
		if($status_paid->id == $status_id){
			$gallery = Gallery::find($pay->gallery_id);
			if(count($gallery) > 0){
				if($gallery->start_moderation == '0000-00-00 00:00:00'){
					$gallery->start_moderation = Carbon::now();
					$gallery->save();
				}
			}
		}
		
		return $pay;
	}
	
	
	/*
	* Скрытие заказа
	*/
	public function hide($id)
	{
		$pay = Pay::find($id);
		$pay->visible = 0;
		$pay->save();
		
		Session::flash('message', 'транзакция удалена');
		return redirect()->route('admin.pay.index');
	}
	
	
	
	/*
	* Выставление статуса оплачено для выбранных записей
	*/
	public function paidAll()
	{
		$extra_field = Request::input('extra_field');
	
		if ($extra_field == Config::get('constants.pay_password')){
			$status_pay= Status::where('type_status', '=', 'pay')->where('caption', '=', 'paid')->first();
			$checkelement = Request::input('checkelement');
			if(count($checkelement) > 0){
				foreach($checkelement as $key => $value){
					$this->changeStatus($value, $status_pay->id);			//вызов функции которая по одной выставляет статус
				}
				
				Session::flash('message', 'Транзакции оплачены');
				$res = array(
					"status" => 'success',
					"message" => 'Транзакции оплачены'
				);
			}else{
				$res = array(
					"status" => 'error',
					"message" => 'Не выбрано ни одного элемента'
				);
			}
		}else{
			if($extra_field != ''){
				$res = array(
					"status" => 'error',
					"message" => 'Неправильный пароль'
				);
			}else{
				$res = array(
					"status" => 'prompt',
					"message" => 'Введите пароль'
				);
			}
			
		}
		
		return Response::json($res);
	}
	
	
	/*
	* Выставление статуса ожидает оплаты для выбранных записей
	*/
	public function waitAll()
	{
		$status_pay= Status::where('type_status', '=', 'pay')->where('caption', '=', 'wait')->first();
		$checkelement = Request::input('checkelement');
		if(count($checkelement) > 0){
			foreach($checkelement as $key => $value){
				$this->changeStatus($value, $status_pay->id);			//вызов функции которая по одной выставляет статус
			}
			
			Session::flash('message', 'Транзакции ожидают оплаты');
			$res = array(
				"status" => 'success',
				"message" => 'Транзакции ожидают оплаты'
			);
		}else{
			$res = array(
				"status" => 'error',
				"message" => 'Не выбрано ни одного элемента'
			);
			
		}
		
		return Response::json($res);
	}
	
	
	/*
	* Выставление статуса отменены админом для выбранных записей
	*/
	public function cancelAll()
	{
		$status_pay= Status::where('type_status', '=', 'pay')->where('caption', '=', 'cancelAdmin')->first();
		$checkelement = Request::input('checkelement');
		if(count($checkelement) > 0){
			foreach($checkelement as $key => $value){
				$this->changeStatus($value, $status_pay->id);			//вызов функции которая по одной выставляет статус
			}
			
			Session::flash('message', 'Транзакции отменены администратором');
			$res = array(
				"status" => 'success',
				"message" => 'Транзакции отменены администратором'
			);
		}else{
			$res = array(
				"status" => 'error',
				"message" => 'Не выбрано ни одного элемента'
			);
			
		}
		
		return Response::json($res);
	}
	
	
	
	/*
	* скрытие записей
	*/
	public function hideAll()
	{
		$checkelement = Request::input('checkelement');
		if(count($checkelement) > 0){
			foreach($checkelement as $key => $value){
				$this->hide($value);			//вызов функции которая по одной скрывает записи
			}
			
			Session::flash('message', 'Транзакции удалены');
			$res = array(
				"status" => 'success',
				"message" => 'Транзакции удалены'
			);
		}else{
			$res = array(
				"status" => 'error',
				"message" => 'Не выбрано ни одного элемента'
			);
			
		}
		
		return Response::json($res);
	}
	
	

}
