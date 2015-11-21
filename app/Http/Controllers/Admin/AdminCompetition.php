<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use Session;
use File;
use Response;
use Request;
use Input;
use App\Competition;
use Carbon\Carbon;


class AdminCompetition extends Controller {

	/*
	* Вывод конкурса
	*/
	public function index()
	{	
		$name = '';
		$text = '';
		$date_start = '';
		$date_end = '';
		$condition = '';
		$start_select = '';
		$end_select = '';
		
		$competition = Competition::first();
		if(count($competition) > 0 ){
			$name = $competition->name;
			$text = $competition->text;
			if($competition->date_start){$date_start = Carbon::parse($competition->date_start)->format('Y-m-d');}
			if($competition->date_end){$date_end = Carbon::parse($competition->date_end)->format('Y-m-d');}
			$condition = $competition->condition;
			if($competition->start_select){$start_select = Carbon::parse($competition->start_select)->format('Y-m-d');}
			if($competition->end_select){$end_select = Carbon::parse($competition->end_select)->format('Y-m-d');}
		}
		
		$data = array(
			"name" => $name,
			"text" => $text,
			"date_start" => $date_start,
			"date_end" => $date_end,
			"condition" => $condition,
			"start_select" => $start_select,
			"end_select" => $end_select,
		);
		return view('admin.competition.index')->with('data', $data);
	}
	
	
	/*
	* Сохранить конкурс
	*/
	public function save()
	{	
		$error = '';
		$name = Request::input('name');
		$text = Request::input('text');
		$date_start = Request::input('date_start');
		$date_end = Request::input('date_end');
		
		if($name == ''){$error .= 'Не заполнено поле "Конкурс"<br>';}
		if($text == ''){$error .= 'Не заполнено поле "Условия конкурса"<br>';}
		if($date_start == ''){$error .= 'Не заполнено поле "Начало конкурса"<br>';}
		if($date_end == ''){$error .= 'Не заполнено поле "Конец конкурса"<br>';}

		if($error == ''){
			$competition = Competition::first();
			if(count($competition) == 0){$competition = new Competition;}
			$competition->name = $name;
			$competition->text = $text;
			$competition->date_start = $date_start;
			$competition->date_end = $date_end;
			$competition->save();
		
		
			Session::flash('message', 'Конкурс сохранен');
		}else{
			Session::flash('message', $error);
		}
		return redirect()->back();
		
	}
	
	
	/*
	* Сохранить дополнительную информацию о конкурсе
	*/
	public function saveExtra()
	{	
		$error = '';
		$condition = Request::input('condition');
		$start_select = Request::input('start_select');
		$end_select = Request::input('end_select');
		
		if($condition == ''){$error .= 'Не выбран "Клиент"<br>';}
		if($start_select == ''){$error .= 'Не заполнено поле "Начало выборки"<br>';}
		if($end_select == ''){$error .= 'Не заполнено поле "Конец выборки"<br>';}

		if($error == ''){
			$competition = Competition::first();
			if(count($competition) == 0){$competition = new Competition;}
			$competition->condition = $condition;
			$competition->start_select = $start_select;
			$competition->end_select = $end_select;
			$competition->save();
		
		
			Session::flash('message2', 'Конкурс сохранен');
		}else{
			Session::flash('message2', $error);
		}
		return redirect()->back();
		
	}
	
}
