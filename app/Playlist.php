<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Image;
use Carbon\Carbon;
use SoapBox\Formatter\Formatter;
use App\Monitor;
use App\Gallery;
use App\Pay;
use App\PlaylistTime;
use File;
use DB;

class Playlist extends Model {

	public $pathPlaylistMonitor_1;	//плейлисты для Экрана 1
	public $pathPlaylistMonitor_2;	//плейлисты для Экрана 2
	public $folderInit;						//папка исходного плейлиста
	public $folderImg;					//путь к картинкам
	public $imgSize;						//размеры для плейлистов
	public $pathImages;					//путь к оригинальным картинкам
	
	
	public $countBlock;					//кол-во логических блоков
	public $timeBlock;					//продолжительность логического блока
	public $timePlaylist;					//продолжительность плейлиста (рассичтывается: $countBlock * $timeBlock)
	public $timeGallery;					//продолжительность показа одного плейлиста
	
	
	
	public function __construct(){
		$this->pathPlaylistMonitor_1 = base_path()."/resources/playlistFiles/Monitor1";
		$this->pathPlaylistMonitor_2 = base_path()."/resources/playlistFiles/Monitor2";
		$this->folderInit = 'init';
		$this->folderImg = 'images';
		$this->imgSize = array(
			'1' => array(
				'w' => 280,
				'h' => 180,
			),
			'2' => array(
				'w' => 240,
				'h' => 192,
			),
		);
		$this->pathImages = base_path()."/public/images";
		
		
		$this->countBlock = 3;
		$this->timeBlock = 300;
		$this->timePlaylist = $this->countBlock * $this->timeBlock;
		$this->timeGallery = 5;
		
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
		return $playlist;
	}
	
	
	
	
	/*
	* -------------------------------------------------------------------------------------------------------------------------
	*											Генерация плейлистов
	* -------------------------------------------------------------------------------------------------------------------------
	*/
	public function initGenerate(){	
		$res1 = 0;
		$res2 = 0;
		
		$res1 = $this->timePlaylist;
		
		return $res1.' - '.$res2;
	}
	
	
	
	
	
	/*
	* -------------------------------------------------------------------------------------------------------------------------
	*											Сохранение исходного плейлиста
	* -------------------------------------------------------------------------------------------------------------------------
	*/
	
	/*
	* initFile - загрузка исходных файлов в базу данных
	*/
	public function initFile(){		
		$res1 = 0;
		$res2 = 0;
		
		$nowDate = Carbon::now();
		$day = sprintf("%02d", $nowDate->day);
		$month = sprintf("%02d", $nowDate->month);
		$nameInitFile = 'ПЛ'.$day.$month.$nowDate->year.'.xjob';
		$nameInitFile = iconv("UTF-8", "cp1251", $nameInitFile);
		
		$res1 = $this->saveInitFile(1, $nameInitFile);
		$res2 = $this->saveInitFile(2, $nameInitFile);


		return $res1.' - '.$res2;
	}
	
	
	/*
	* saveInitFile - сохранение исходного плейлиста
	*/
	public function saveInitFile($monitorNumber, $nameInitFile){	
		$res = 0;
		
		$monitorId = $this->getId($monitorNumber);
		if($monitorNumber == 1){
			$path = $this->pathPlaylistMonitor_1.'/'.$this->folderInit;
		}
		if($monitorNumber == 2){
			$path = $this->pathPlaylistMonitor_2.'/'.$this->folderInit;
		}
		
		$pathFileInit = $path.'/'.$nameInitFile;
		if (File::exists($pathFileInit)){
			$this->deleteInitPlaylist($monitorId);
			$res = $this->saveFileInDB($pathFileInit, $monitorId);
		}
		
		/* Очистка старых исходных файлов */
		foreach(File::files($path) as $key => $file){
			if($pathFileInit != $file){
				File::delete($file);
			}
		}
		
		return $res;
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
		$res = 0;
		
		$contents = File::get($file);
		$formatter = Formatter::make($contents, Formatter::XML);
		$arrContent   = $formatter->toArray();
		
		$arrIdblock = array();
		
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

			
			
			/* Подсчет блоков */
			if(!array_key_exists($name, $arrIdblock) and count($arrIdblock) == 0){
				$arrIdblock[$name] = 1;
				$idblock = 1;
			}elseif(array_key_exists($name, $arrIdblock)){
				$arrIdblock[$name] += 1;	
				$idblock = $arrIdblock[$name];
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
			$playlist->idblock = $idblock;
			$playlist->save();
			
			//print '<pre>';
			//print_r($arrIdblock);
			//print '</pre>';
			
			$res = 1;
		}
		return $res;
	}
	
	
	/*
	* -------------------------------------------------------------------------------------------------------------------------
	*											Общие функции
	* -------------------------------------------------------------------------------------------------------------------------
	*/
	
	
	/*
	* Получение номера экрана по id
	*/
	public function getNumber($monitorId) {
		$res = '';
		$Monitor = Monitor::where('id', '=', $monitorId)->first();
		if($Monitor){
			$res = $Monitor->number;
		}
		return $res;
	}
	
	/*
	* Получение id экрана по номеру
	*/
	public function getId($number) {
		$res = '';
		$Monitor = Monitor::where('number', '=', $number)->first();
		if($Monitor){
			$res = $Monitor->id;
		}
		return $res;
	}
	
	
	
	/*
	* сортировка массив
	*/
	public function array_orderby() {
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
				}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}

}
