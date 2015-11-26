<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use Session;
use File;
use Response;
use Request;
use Input;
use App\Competition;
use App\Gallery;
use App\User;
use Carbon\Carbon;


class CompetitionController extends Controller {

	/*
	* Вывод конкурса
	*/
	public function index(Gallery $galleryModel, User $userModel)
	{	
		$data = array();
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
			if($competition->date_end && $competition->date_end != '0000-00-00 00:00:00'){$date_end = Carbon::parse($competition->date_end)->format('Y-m-d');}
			$condition = $competition->condition;
			if($competition->start_select){$start_select = Carbon::parse($competition->start_select)->format('Y-m-d');}
			if($competition->end_select){$end_select = Carbon::parse($competition->end_select)->format('Y-m-d');}
		}
		
		$arrRes = $galleryModel->getGalleryCompetition();
		$top = $galleryModel->getTop($arrRes, 10);
		$autor = $galleryModel->getAutor($arrRes);
		$dopGallery= $galleryModel->getGalleryCompetitionAll();
		
		$data = array(
			"name" => $name,
			"text" => $text,
			"date_start" => $date_start,
			"date_end" => $date_end,
			"condition" => $condition,
			"start_select" => $start_select,
			"end_select" => $end_select,
			"pathImages" => $galleryModel->pathImages,
			"top" => $top,
			"autor" => $autor,
			"gallery" => $dopGallery,
		);
		
		return view('pages.competition.index')
			->with('defaultAvatar', $userModel->defaultAvatar)
			->with('data', $data);
	}
	
	
	
	/*
	* Детальная страница пользователя с его картинками
	*/
	public function show(Gallery $galleryModel, $id){
		$arrRes = $galleryModel->getGalleryCompetition();
		$autorGallery = $galleryModel->getAutorGallery($arrRes, $id);
		
		$data = array(
			'user' => User::find($id),
			'gallery' => $autorGallery,
			'pathImages' => $galleryModel->pathImages,
		);
		return view('pages.competition.show')->with('data', $data);
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

		$edit = 0;
		
		if($error == ''){
			$competition = Competition::first();
			if(count($competition) == 0){
				$competition = new Competition;
				$edit = 1;
			}else{
				if(Carbon::parse($competition->date_end)->timestamp <= Carbon::now()->timestamp){
					$edit = 1;
				}
			}
			
			// Если можно редактировать
			if($edit == 1){
				$competition->name = $name;
				$competition->text = $text;
				$competition->date_start = $date_start;
			}
			
			//Если новая дата конца конкурса не меньше уже которой есть
			if(!$competition->date_end || Carbon::parse($competition->date_end)->timestamp <= Carbon::parse($date_end)->timestamp){
				$competition->date_end = $date_end;
			}
			
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
