<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use SoapBox\Formatter\Formatter;
use App\Monitor;
use App\Gallery;
use App\Pay;
use File;
use DB;

class Playlist extends Model {

	public $error;
	public $pathPlaylistMonitor_1;	//Исходный плейлист для Экрана 1
	public $pathPlaylistMonitor_2;	//Исходный плейлист для Экрана 2
	public $dateStart;						//Время показа для генерируемых плейлистов
	public $dateEnd;						//Конечное время показа для генерируемых плейлистов
	
	
	public function __construct(){
		$this->error = array();
		$this->pathPlaylistMonitor_1 = base_path()."/resources/playlistFiles/Monitor1";
		$this->pathPlaylistMonitor_2 = base_path()."/resources/playlistFiles/Monitor2";

		
		//Интервал каждые пол часа
		$nowDate = Carbon::now();
		$addHour = 0;
		$startMinute = 0;
		$endMinute = 30;
		if($nowDate->minute >= 30){
			$addHour = 1;
			$startMinute = 30;
			$endMinute = 0;
		}
		$this->dateStart = $nowDate->second(0)->minute($startMinute)->toDateTimeString();
		$this->dateEnd = $nowDate->second(0)->minute($endMinute)->addHour($addHour)->toDateTimeString();
	
	}
	
	public function monitor(){
        return $this->belongsTo('App\Monitor');
    }
	
	
	/*
	* Получение исходного плейлиста из БД
	*/
	public function getInitPlaylist(){
		$playlist = $this
			->with('monitor')
			->where('type', '=', '0')
			->orderBy('monitor_id', 'asc')
			->orderBy('sort', 'asc')
			->get();
		//dd($playlist);
		return $playlist;
	}
	
	
	/*
	* Заказы в очередь на генерацию плейлиста
	*/
	public function getGalleryGeneration($monitorId = ''){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		
		$gallery = Gallery::select(DB::raw('galleries.*'))
			->join('tarifs', 'tarifs.id', '=', 'galleries.tarif_id')
			->where('status_main', '=', $status_main->id)
			->where('count_show', '>', '0')
			->where('monitor_id', '=', $monitorId)
			->where('date_show', '>=', $this->dateStart)
			->get();
		//dd($gallery);
		return $gallery;
	}
	
	
	/*
	* Определение и загрузка исходных файлов в базу данных
	*/
	public function initFile(){
		$Monitor_1 = Monitor::where('number', '=', 1)->first();
		$Monitor_2 = Monitor::where('number', '=', 2)->first();
		
		// для первого экрана
		$this->deleteInitPlaylist($Monitor_1->id);
		$files = File::files($this->pathPlaylistMonitor_1);
		foreach($files as $key => $file){
			$this->saveFileInDB($file, $Monitor_1->id);
		}
		
		// для второго экрана
		$this->deleteInitPlaylist($Monitor_2->id);
		$files = File::files($this->pathPlaylistMonitor_2);
		foreach($files as $key => $file){
			$this->saveFileInDB($file, $Monitor_2->id);
		}
		
		return 1;
	}
	
	
	/*
	* Удаление исходного плейлиста
	*/
	public function deleteInitPlaylist($monitorId = ''){
		$res = false;
		if($monitorId != ''){
			$res = true;
			$playlist = $this->where('type', '=', '0')->where('monitor_id', '=', $monitorId);
			$playlist->delete();
		}
		return $res;
	}
	
	/*
	* Сохранение файла в базу данных
	*/
	public function saveFileInDB($file, $monitorId){
		$contents = File::get($file);
		$formatter = Formatter::make($contents, Formatter::XML);
		$arrContent   = $formatter->toArray();
		
		foreach($arrContent['collection']['item'] as $key => $itemTemp){
			$item = $itemTemp['@attributes'];
	
			$type = 0;	//Флаг что плейлист исходный
			$enable = '';
			$name = '';
			$loop = '';
			$IsTime = 'true'; //По умолчанию true так как у многих элементов аттрибут IsTime отсуствует
			$time = '';
			
			if (array_key_exists('enable', $item)) {$enable = $item['enable'];}
			if (array_key_exists('name', $item)) {$name = $item['name'];}
			if (array_key_exists('loop', $item)) {$loop = $item['loop'];}
			if (array_key_exists('IsTime', $item)) {$IsTime = $item['IsTime'];}
			if (array_key_exists('time', $item)) {$time = $item['time'];}
			
			if($enable == 'true' OR $enable == 'True'){
				$enable = 1;
			}else{
				$enable = 0;
			}
			
			if ($IsTime == 'true' OR $IsTime == 'True') {
				$IsTime = 1;
			}else{
				$IsTime = 0;
			}
			
			$playlist = new Playlist;
			$playlist->id = $type;
			$playlist->type = $type;
			$playlist->enable = $enable;
			$playlist->name = $name;
			$playlist->loop_xml = $loop;
			$playlist->is_time = $IsTime;
			$playlist->time = $time;
			$playlist->monitor_id = $monitorId;
			$playlist->sort = $key*10;
			$playlist->save();
			
		}
	}
	
	
	/*
	* Тестовое заполенения базы данных галлереей
	* Не использовать на рабочем сайте
	*/
	public function testGalleryUpload(){
		$nowDate = Carbon::now();
		$tarif = array(
			'1' => 12, 
			'2' => 16,
			'3' => 48 
		);

		for($i = 31; $i <= 100; $i++){
			$id = $i;
			$date_show = $nowDate->addMinutes(1)->toDateTimeString();
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
