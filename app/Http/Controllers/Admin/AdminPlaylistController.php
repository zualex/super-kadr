<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Response;
use Carbon\Carbon;
use App\Pay;
use App\Gallery;
use App\Playlist;
use Session;

class AdminPlaylistController extends Controller {


	public function index(Playlist $playlistModel)
	{
		//$this->testGalleryUpload();
		$data = array(
			'initPlaylist' => $playlistModel->getInitPlaylist(),
			'galleryGeneration_1' => $playlistModel->getGalleryGeneration(1),
			'galleryGeneration_2' => $playlistModel->getGalleryGeneration(2),
		);
		return view('admin.playlist.index')->with('data', $data);
	}

	
	public function initFile()
	{
		$playlist = new Playlist;
		return $playlist->initFile();
	}
	
	
	// удаление записи
	public function delete($id){
		$playlist = Playlist::find($id);
		if($playlist){
			$playlist->delete();
			Session::flash('message', 'Запись удалена');
		}
		return redirect()->route('admin.playlist.index');
	}
	
	//Изменение состояния
	public function enable($id){
		$playlist = Playlist::find($id);
		$enable = Request::input('value');
		if($playlist){
			$playlist->enable = $enable;
			$playlist->save();
		
			$res = array(
				"status" => 'success',
				"message" => 'Изменения сохранены'
			);
		}else{
			$res = array(
				"status" => 'error',
				"message" => 'Произошла ошибка. Изменения не сохранены'
			);
		}

		
		return Response::json($res);
	}
	
	
	//Изменение IsTime
	public function isTime($id){
		$playlist = Playlist::find($id);
		$isTime = Request::input('value');
		if($playlist){
			$playlist->is_time = $isTime;
			$playlist->save();
		
			$res = array(
				"status" => 'success',
				"message" => 'Изменения сохранены'
			);
		}else{
			$res = array(
				"status" => 'error',
				"message" => 'Произошла ошибка. Изменения не сохранены'
			);
		}

		
		return Response::json($res);
	}
	
	
	
	
	
	
	/*
	* Тестовое заполенения базы данных галлереей
	* Не использовать на рабочем сайте
	*/
	public function testGalleryUpload(){
		$nowDate = Carbon::now();
		$nowDate = $nowDate->hour(0)->minute(0)->second(0);
		
		$tarif = array(
			'1' => 12, 
			'2' => 20,
			'3' => 48 
		);

		for($i = 1; $i <= 50; $i++){
			$id = $i;
			$date_show = $nowDate->addMinutes(5)->toDateTimeString();
			$tarif_id = $i%3+1;
			
			/*$Gallery = new Gallery;
			$Gallery->id = $id;
			$Gallery->user_id = 1;
			$Gallery->src =  '1.jpeg';
			$Gallery->status_main =  6;
			$Gallery->status_order =  11;
			$Gallery->count_show =  $tarif[$tarif_id];
			$Gallery->date_show =  $date_show;
			$Gallery->tarif_id =  $tarif_id;
			$Gallery->monitor_id =  1;
			$Gallery->save();
			
			$Pay = new Pay;
			$Pay->gallery_id = $id;
			$Pay->status_pay = 1;
			$Pay->name = 'Пользователь: 1 сделал заказ';
			$Pay->price = 150;
			$Pay->visible = 1;
			$Pay->save();*/
		
		}
	}
	
	

}
