<?php namespace App\Http\Controllers\Admin;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Response;
use Carbon\Carbon;
use App\Pay;
use App\Gallery;
use App\Playlist;
use App\Tarif;
use App\Monitor;
use App\PlaylistExtraVideo;
use Session;
class AdminPlaylistController extends Controller {
	public function index(Playlist $playlistModel)
	{
		//$this->testGalleryUpload();
		$pathImages = $playlistModel->pathImages;
		$folderName = str_replace('/', '\\', $pathImages);		//Полный путь к папке
		$pathImages = str_replace(base_path(), '', $pathImages);		//путь для картинок
		
		$initPlaylist = $playlistModel->getInitPlaylist();
		$tarifTemp = Tarif::all();
		$tarif = array();
		foreach ($tarifTemp as $key => $item){
			$tarif[$item['id']] = $item;
		}
		
		$extraVideo = array();
		$PlaylistExtraVideo = PlaylistExtraVideo::all();
		if(count($PlaylistExtraVideo) > 0){
			foreach($PlaylistExtraVideo as $key => $item){
				$extraVideo[$item->id] = array(
					'path'=>$item->path,
					'time'=>$item->time,
				);
			}
		}else{
			$this->initExtraVideo();
			$PlaylistExtraVideo = PlaylistExtraVideo::all();
			foreach($PlaylistExtraVideo as $key => $item){
				$extraVideo[$item->id] = array(
					'path'=>$item->path,
					'time'=>$item->time,
				);
			}
		}
		
		
		$info = $playlistModel->getInfoPlaylist($playlistModel->getId(1));
		$dateStart1 = $info['dateStart'];
		$playlistFinaly1 = $playlistModel->getGenerateArray(1);
		
		$info = $playlistModel->getInfoPlaylist($playlistModel->getId(2));
		$dateStart2 = $info['dateStart'];
		$playlistFinaly2 = $playlistModel->getGenerateArray(2);
		
		//dd($playlistFinaly1);
		
		$data = array(
			'pathImages' => $pathImages,					//Путь к картинкам
			'folderName' => $folderName,					//Путь к картинкам
			'initPlaylist' => $initPlaylist,						//Исходный плейлист
			'tarif' => $tarif,											//Массив с тарифами
			
			'extraVideo' => $extraVideo,						//Массив с доп роликами
			
			'playlistFinaly1' => $playlistFinaly1,	//Заказы в очередь для первого экрана
			'dateStart1' => $dateStart1,						//Дата начала формирования плейлиста
			'playlistFinaly2' => $playlistFinaly2,
			'dateStart2' => $dateStart2,
		);
		return view('admin.playlist.index')->with('data', $data);
	}
	
	public function initFile()
	{
		$playlist = new Playlist;
		return $playlist->initFile();
	}
	
	
	public function initGenerate()
	{
		$playlist = new Playlist;
		return $playlist->initGenerate();
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
			$Gallery->monitor_id =  2;
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
	
	
	
	/*
	* Сохранение дополнительных роликов
	*/
	public function saveExtraVideo(){
		
		$resAll = Request::except('_token');
		$result = 1;
		
		foreach($resAll['path'] as $id => $value){
			$PlaylistExtraVideo = PlaylistExtraVideo::where('id', '=', $id)->first();
			if(count($PlaylistExtraVideo) == 0){$PlaylistExtraVideo = new PlaylistExtraVideo;}
			$PlaylistExtraVideo->path = $value;
			$PlaylistExtraVideo->time = $resAll['time'][$id];
			$PlaylistExtraVideo->save();
		}
		
		
		
		
		Session::flash('message', 'Настройки сохранены');
		$res = array(
			"status" => 'success',
			"message" => 'Настройки сохранены'
		);
		return Response::json($res);
	}
	
	
	
	public function deleteExtraVideo($id){
		$PlaylistExtraVideo = PlaylistExtraVideo::where('id', '=', $id)->first();
		$PlaylistExtraVideo->delete();
		Session::flash('message', 'Дополнительный ролик удален');
		return redirect()->route('admin.playlist.index');
	}
	
	
	
	public function initExtraVideo(){
		$PlaylistExtraVideo = PlaylistExtraVideo::where('id', '=', 1)->first();
		if(count($PlaylistExtraVideo) == 0){
			$PlaylistExtraVideo = new PlaylistExtraVideo;
			$PlaylistExtraVideo->id = 1;
			$PlaylistExtraVideo->path = '';
			$PlaylistExtraVideo->time = '';
			$PlaylistExtraVideo->save();
		}
		
		
		$PlaylistExtraVideo = PlaylistExtraVideo::where('id', '=', 2)->first();
		if(count($PlaylistExtraVideo) == 0){
			$PlaylistExtraVideo = new PlaylistExtraVideo;
			$PlaylistExtraVideo->id = 2;
			$PlaylistExtraVideo->path = '';
			$PlaylistExtraVideo->time = '';
			$PlaylistExtraVideo->save();
		}
		
		
		$PlaylistExtraVideo = PlaylistExtraVideo::where('id', '=', 3)->first();
		if(count($PlaylistExtraVideo) == 0){
			$PlaylistExtraVideo = new PlaylistExtraVideo;
			$PlaylistExtraVideo->id = 3;
			$PlaylistExtraVideo->path = '';
			$PlaylistExtraVideo->time = '';
			$PlaylistExtraVideo->save();
		}
		
		
		$PlaylistExtraVideo = PlaylistExtraVideo::where('id', '=', 4)->first();
		if(count($PlaylistExtraVideo) == 0){
			$PlaylistExtraVideo = new PlaylistExtraVideo;
			$PlaylistExtraVideo->id = 4;
			$PlaylistExtraVideo->path = '';
			$PlaylistExtraVideo->time = '';
			$PlaylistExtraVideo->save();
		}
		
		
		$PlaylistExtraVideo = PlaylistExtraVideo::where('id', '=', 5)->first();
		if(count($PlaylistExtraVideo) == 0){
			$PlaylistExtraVideo = new PlaylistExtraVideo;
			$PlaylistExtraVideo->id = 5;
			$PlaylistExtraVideo->path = '';
			$PlaylistExtraVideo->time = '';
			$PlaylistExtraVideo->save();
		}
	}
	
	
	public function checkdate(Playlist $playlistModel)
	{
		$tarif_id = Request::input('tarif_id');
		$monitor_id = Request::input('monitor_id');
		$dateDay = Request::input('dateDay');
		
		//return $playlistModel->availabilityDate(1, 1, '06.10.2015');
		return $playlistModel->availabilityDate($tarif_id, $monitor_id, $dateDay);
	}
}