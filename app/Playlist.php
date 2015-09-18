<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use SoapBox\Formatter\Formatter;
use App\Monitor;
use App\Gallery;
use App\Pay;
use App\PlaylistTime;
use File;
use DB;

class Playlist extends Model {

	public $error;
	public $pathPlaylistMonitor_1;	//Исходный плейлист для Экрана 1
	public $pathPlaylistMonitor_2;	//Исходный плейлист для Экрана 2
	
	public $timeInit;						//5 минут из исходного плейлиста
	public $countGallery;				//5 показов наших заказов
	public $timeGallery;					//5 секунд показ заказов
	
	public $infoPlayist;					//Информация о генерируемых плейлистах
	public $dateStart;						//Время показа для генерируемых плейлистов
	public $dateEnd;						//Конечное время показа для генерируемых плейлистов
	public $allSecond;					//общее кол-во секунд на генерацию одного прайса
	
	
	public function __construct(){
		$this->error = array();
		$this->pathPlaylistMonitor_1 = base_path()."/resources/playlistFiles/Monitor1";
		$this->pathPlaylistMonitor_2 = base_path()."/resources/playlistFiles/Monitor2";

		$this->timeInit = 300;
		$this->countGallery = 5;
		$this->timeGallery = 5;
		
		$this->infoPlayist = array();
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
	* Инициализация даты начала и даты конца плейлиста и общее кол-во секунд на генерацию одного прайса
	*/
	public function dateInit($monitorId = ''){
		$playlist = Playlist::select(DB::raw('SUM(playlists.time) as allTime, loop_xml'))
			->where('enable', '=', 1)
			->where('is_time', '=', 1)
			->where('monitor_id', '=', $monitorId)
			->groupBy('loop_xml')
			->get();
		$allSecond = 0;
		if(count($playlist) > 0){
			foreach ($playlist as $key => $value){
				$allSecond += $value->allTime*($value->loop_xml + 1);
			}
		}
		$countPlaylist = ceil($allSecond/$this->timeInit);
		$dopTime = $countPlaylist*$this->timeGallery;		//Узнаем дополнительное время с учетом заказов
		$allSecond += $dopTime;
		
		$nowDate = Carbon::now();
		$playlistTime = PlaylistTime::where('dateStart', '<=', $nowDate->toDateTimeString())
			->where('dateEnd', '>=', $nowDate->toDateTimeString())
			->first();
			
		$dateStart = '';
		$dateEnd = '';
		if($playlistTime){
			$dateStart = $playlistTime->dateStart;
			$dateEnd = $playlistTime->dateEnd;
		}
		
		$this->infoPlayist[$monitorId] = array(
			'dateStart' =>  $dateStart,
			'dateEnd'   =>  $dateEnd,
			'allSecond' =>  $allSecond,
		);

		return $playlist;
    }
	
	
	
	/*
	* Определение и загрузка исходных файлов в базу данных
	*/
	public function initFile(){		
		$Monitor_1 = Monitor::where('number', '=', 1)->first();
		$Monitor_2 = Monitor::where('number', '=', 2)->first();
		
		$this->dateInit($Monitor_1->id);			//Инициализация даты начала и даты конца плейлиста и общее кол-во секунд на генерацию одного прайса
		$this->dateInit($Monitor_2->id);			//Инициализация даты начала и даты конца плейлиста и общее кол-во секунд на генерацию одного прайса
		
		// для первого экрана очистка и сохранение в базу данных
		$this->deleteInitPlaylist($Monitor_1->id);
		$files = File::files($this->pathPlaylistMonitor_1);
		foreach($files as $key => $file){
			$this->saveFileInDB($file, $Monitor_1->id);
		}
		
		// для второго экрана очистка и сохранение в базу данных
		$this->deleteInitPlaylist($Monitor_2->id);
		$files = File::files($this->pathPlaylistMonitor_2);
		foreach($files as $key => $file){
			$this->saveFileInDB($file, $Monitor_2->id);
		}
		
		//заносим в базу данных даты начала и конца генерации плейлистов
		$this->initPlaylistTime($Monitor_1->id);
		$this->initPlaylistTime($Monitor_2->id);
		return 1;
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
	*/
	public function getGalleryGeneration($monitorId = ''){

		$this->dateInit($monitorId);	//инициализация даты начала и даты конца плейлиста
		
		
		$dateStart = $this->infoPlayist[$monitorId]['dateStart'];
		$dateEnd = $this->infoPlayist[$monitorId]['dateEnd'];
		$diffDate= Carbon::parse($dateStart)->diffInSeconds(Carbon::parse($dateEnd));	//Разница в секундах между датой начала и конца
		$timeOnePlayList = $this->timeInit + ($this->countGallery*$this->timeGallery);		//Время одного прогона плейлиста с учетом заказов
		$allCountPlaylist = ceil($diffDate/$timeOnePlayList);												//Кол-во прогонов плейлиста
		
		
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		$gallery = Gallery::select(DB::raw('galleries.*, tarifs.hours, tarifs.interval_sec'))
			->join('tarifs', 'tarifs.id', '=', 'galleries.tarif_id')
			->where('status_main', '=', $status_main->id)
			->where('count_show', '>', '0')
			->where('monitor_id', '=', $monitorId)
			->where('date_show', '<=', $dateEnd)
			->orderBy('date_show', 'asc')
			->get();
			
			
		// все заказы которые удовлетворяют условиям заносим в массив	
		$arrGallery = array();
		if(count($gallery) > 0){
			foreach($gallery as $key => $item){				
				$arrGallery[$item->id] = array(
					"id" => $item->id,
					"src" => $item->src,
					"count_show" => $item->count_show,
					"date_show" => $item->date_show,
					"hours" => $item->hours,
					"interval_sec" => $item->interval_sec,
					"monitor_id" => $item->monitor_id,
					"sort" => 0,
					"countPlaylist" => 9999,
				);
			}
		}		

		
		//в $arrRes собираем заказы по 5 штук 
		$arrRes = array();
		$arrGalleryTemp = array();
		if(count($arrGallery) > 0){	
			for($countPlaylist = 1; $countPlaylist <=$allCountPlaylist; $countPlaylist++){
				foreach($arrGallery as $key => $item){			
					$item['count_show'] = $item['count_show'] - $countPlaylist + 1;			//фикс для корректной работы getSort
	
					$sort = $this->getSort($countPlaylist, $item);
					if($sort > 0 AND $item['count_show'] > 0 AND !array_key_exists($item['id'], $arrGalleryTemp)){
						$arrGalleryTemp[$item['id']]['id'] = $item['id'];
						$arrGalleryTemp[$item['id']]['src'] = $item['src'];
						$arrGalleryTemp[$item['id']]['count_show'] = $item['count_show'] - 1;
						$arrGalleryTemp[$item['id']]['date_show'] = $item['date_show'];
						$arrGalleryTemp[$item['id']]['hours'] = $item['hours'];
						$arrGalleryTemp[$item['id']]['interval_sec'] = $item['interval_sec'];
						$arrGalleryTemp[$item['id']]['monitor_id'] = $item['monitor_id'];
						
						$arrGalleryTemp[$item['id']]['sort'] = $sort;
						$arrGalleryTemp[$item['id']]['countPlaylist'] = $countPlaylist;
					}
				}
				
				$arrGalleryTemp = $this->checkOneIter($arrGalleryTemp, $countPlaylist);	//Сортировка по полю sort и отсеиваем если больше 5 заказов
				$arrRes[$countPlaylist] = $arrGalleryTemp;
				
			}
		}

		


		dd($arrRes);
		return $gallery;
	}
	
	
	
	/*
	* Вычисление коэффициента вероятности показа галлереи
	* Если значение больше 100 то должен быть показан
	*/
	public function getSort($countPlaylist, $item){
		$sort = 0;
		
		$dateStart = $this->infoPlayist[$item['monitor_id']]['dateStart'];
		
		$dateInit = Carbon::parse($dateStart)->addSeconds(($countPlaylist-1) * $this->timeInit);		//Узнаем дату начала пятиминутки
		if(Carbon::parse($item['date_show'])->timestamp <= $dateInit->timestamp){								//Если дата показа меньше или равно дате начала пятиминутки то включаем заказ
			$intervalAll = $countPlaylist * $this->timeInit;																				//Узнаем для пятиминутки общий интервал 
			$tarifCountShow = $item['hours']*60*60/$item['interval_sec'];															//Узнаем сколько по тарифу должно быть показов
			
			$diffSec = Carbon::parse($item['date_show'])->diffInSeconds($dateInit);										//Узнаем разницу между датой показа и датой формируемого плейлиста
			$abstractCount = ceil($diffSec/$item['interval_sec']);																		//Узнаем сколько должно было быть показов
			$diffCount = $abstractCount - ($tarifCountShow - $item['count_show']);											//Узнаем разницу между сколько должно быть и сколько показалось товаров
			
			$useInterval = ($tarifCountShow - $item['count_show'] + 1) * $item['interval_sec']; 							//Узнаем используемый интервал
			$sort = $intervalAll/$useInterval * $diffCount * 100;																		//Отношение общего интервала к интервалу показа и умножить коэффициент
		}
		
		
		return $sort;
	}
	
	
	
	/*
	*	Сортировка по полю sort и отсеиваем если больше 5 заказов
	*/
	public function checkOneIter($arrGallery, $countPlaylist){
		$arrGallery = $this->array_orderby($arrGallery, 'countPlaylist', SORT_ASC, 'sort', SORT_DESC);
		$counter = 0;
		foreach($arrGallery as $key => $value){
			if($value['countPlaylist'] == $countPlaylist){
				$counter += 1;
				if($counter > $this->countGallery){
					unset($arrGallery[$key]);
				}		
			}else{
				unset($arrGallery[$key]);
			}

		}
		return $arrGallery;
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
	* Сохранение в базу данных даты начала и конца генерации плейлистов
	*/
	public function initPlaylistTime($monitorId = ''){
		$playlistTime = PlaylistTime::where('monitor_id', '=', $monitorId);
		$playlistTime->delete();
		
		$allSecond = $this->infoPlayist[$monitorId]['allSecond'];
		$arrDate = array();
		
		
		if($allSecond > 0){
			$nowDate = Carbon::now();
			$dateStart = $nowDate->hour(0)->minute(0)->second(0);
			$dateEnd = $dateStart;
			$dayNow = $dateStart->day;
			
			$i = 0;
			while($dayNow == $dateEnd->day){
				$i++;
				$dateStartStr = $dateEnd->toDateTimeString();
				$dateEnd = $dateEnd->addSeconds($allSecond);
				if($dayNow != $dateEnd->day){
					$dateEnd = $dateEnd->hour(0)->minute(0)->second(0);
				}
				
				$arrDate[] = array(
					'dateStart' => $dateStartStr,
					'dateEnd' => $dateEnd->toDateTimeString(),
				);				
			}
			

			if(count($arrDate) > 0){
				foreach($arrDate as $key => $value){
					$playlistTime = new PlaylistTime;
					$playlistTime->number = $key;
					$playlistTime->dateStart = $value['dateStart'];
					$playlistTime->dateEnd = $value['dateEnd'];
					$playlistTime->monitor_id = $monitorId;
					$playlistTime->save();
				}
			}

		}
		
			
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
	* ---------------------------------------------------------------------------------------------
	*/
	
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

		for($i = 1; $i <= 70; $i++){
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
