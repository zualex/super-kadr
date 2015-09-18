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
	
	public $timeInit;						//5 минут из исходного плейлиста
	public $countGallery;				//5 показов наших заказов
	public $timeGallery;					//5 секунд показ заказов
	
	
	public function __construct(){
		$this->error = array();
		$this->pathPlaylistMonitor_1 = base_path()."/resources/playlistFiles/Monitor1";
		$this->pathPlaylistMonitor_2 = base_path()."/resources/playlistFiles/Monitor2";
		
		$this->timeInit = 300;
		$this->countGallery = 5;
		$this->timeGallery = 5;
		
	}
	
	public function monitor(){
        return $this->belongsTo('App\Monitor');
    }
	
	
	/*
	* Инициализация даты начала и даты конца плейлиста
	*/
	public function dateInit($monitorId = ''){
		$playlist = Playlist::select(DB::raw('SUM(playlists.time) as allTime, loop_xml'))
			->where('enable', '=', 1)
			->where('is_time', '=', 1)
			->where('monitor_id', '=', $monitorId)
			->groupBy('loop_xml')
			->get();
		$allTime = 0;
		if(count($playlist) > 0){
			foreach ($playlist as $key => $value){
				$allTime += $value->allTime*($value->loop_xml + 1);
			}
		}
		$countPlaylist = ceil($allTime/$this->timeInit);
		$dopTime = $countPlaylist*$this->timeGallery;		//Узнаем дополнительное время с учетом заказов
		$allTime += $dopTime;

		$nowDate = Carbon::now();
		$this->dateStart = $nowDate->toDateTimeString();
		$this->dateEnd = $nowDate->addSeconds($allTime)->toDateTimeString();

		return $playlist;
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
	* Заказы в очередь на генерацию плейлиста
	* Определение порядка зависит от коэффициентов. Например есть 3 тарифа:
	* 		- 1й.Тариф «Просто» включает 12 показов в течение часа.(каждые 5 мин.)
	* 		- 2й.Тариф «Забавно» 4 показа в час в течение 5 часов.( каждые 15 мин.)
	* 		- 3й.Тариф «Весело» 2 показа в час в течение суток( каждые 30 мин)
	*
	* Генерация плейлистов:
	* 		по порядку 5 минут из исходного плейлиста + 5 показов по 5 секунд  из  заказов
	*
	* То есть вероятность попадания заказа с тарифом 1 в первую 5-ти минутка = 100%
	*																							    с тарифом 2 = 33%
	*																							    с тарифом 3 = 16%
	*
	*
	*/
	public function getGalleryGeneration($monitorId = ''){

		$this->dateInit($monitorId);	//инициализация даты начала и даты конца плейлиста
		
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		
		$gallery = Gallery::select(DB::raw('galleries.*, tarifs.hours, tarifs.interval_sec'))
			->join('tarifs', 'tarifs.id', '=', 'galleries.tarif_id')
			->where('status_main', '=', $status_main->id)
			->where('count_show', '>', '0')
			->where('monitor_id', '=', $monitorId)
			->where('date_show', '<=', $this->dateEnd)
			->orderBy('date_show', 'asc')
			->get();
			
		$countPlaylist = 1;			//Какая по счету пятиминутка
		$arrGallery = array();
		$arrSort = array();
		if(count($gallery) > 0){
			foreach($gallery as $key => $item){				
				$sort = $this->getSort($countPlaylist, $item);
				if($sort >= 100){
					$arrSort[$item->id] = $sort;
					$arrGallery[$item->id] = array(
						"id" => $item->id,
						"src" => $item->src,
						"count_show" => $item->count_show,
						"date_show" => $item->date_show,
						"hours" => $item->hours,
						"interval_sec" => $item->interval_sec,
						"sort" => $sort,
					);
					$arrGallery = $this->array_orderby($arrGallery, 'sort', SORT_DESC);
					
					
				}
			}
		}

		dd($arrGallery);
		return $gallery;
	}
	
	
	/*
	* сортировка массив
	*/
	function array_orderby() {
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
	
	
	/*
	* Вычисление коэффициента вероятности показа галлереи
	* Если значение больше 100 то должен быть показан
	*/
	public function getSort($countPlaylist, $item){
		$sort = 0;
			
		$dateInit = Carbon::parse($this->dateStart)->addSeconds(($countPlaylist-1) * $this->timeInit);		//Узнаем дату начала пятиминутки
		if(Carbon::parse($item->date_show)->timestamp <= $dateInit->timestamp){								//Если дата показа меньше или равно дате начала пятиминутки то включаем заказ
			$intervalAll = $countPlaylist * $this->timeInit;																				//Узнаем для пятиминутки общий интервал 
			$tarifCountShow = $item->hours*60*60/$item->interval_sec;															//Узнаем сколько по тарифу должно быть показов
			
			$diffSec = Carbon::parse($item->date_show)->diffInSeconds($dateInit);										//Узнаем разницу между датой показа и датой формируемого плейлиста
			$abstractCount = ceil($diffSec/$item->interval_sec);																		//Узнаем сколько должно было быть показов
			$diffCount = $abstractCount - ($tarifCountShow - $item->count_show);											//Узнаем разницу между сколько должно быть и сколько показалось товаров
			
			$useInterval = ($tarifCountShow - $item->count_show + 1) * $item->interval_sec; 							//Узнаем используемый интервал
			$sort = $intervalAll/$useInterval * $diffCount * 100;																		//Отношение общего интервала к интервалу показа и умножить коэффициент
		}
		
		return $sort;
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
			'2' => 20,
			'3' => 48 
		);

		for($i = 12; $i <= 70; $i++){
			$id = $i;
			$date_show = $nowDate->addMinutes(4)->toDateTimeString();
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
