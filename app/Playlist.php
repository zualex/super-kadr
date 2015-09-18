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
	* initFile - загрузка исходных файлов в базу данных
	*/
	public function initFile(){		
		$Monitor_1 = Monitor::where('number', '=', 1)->first();
		$Monitor_2 = Monitor::where('number', '=', 2)->first();
		
		/*
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
		*/
		
		//Генерация первого файла плейлиста
		$this->generationNewPlay($Monitor_1->id);
		$this->generationNewPlay($Monitor_2->id);

		return 1;
	}
	
	
	
	/*
	* generationNewPlay - генерация файла плейлиста
	* $offset = 0 - генерация плейлиста для текущей даты
	* $offset = 1 - генерация плейлиста для следущей даты
	*/
	public function generationNewPlay($monitorId = '', $offset = 0){
		$this->getDateNext($monitorId, $offset);									//Формирование в $this->infoPlayist информации следующего плейлиста
		$arrAddGallery = $this->getArrAddGallery($monitorId);				//Получение списка добавляемых заказов для данного плейлиста
		dd($arrAddGallery);
		
		
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
		return 1;
	}
	
	
	
	/*
	* getDateNext - Инициализация даты начала и даты конца плейлиста и общее кол-во секунд на генерацию одного прайса
	* $offset = 0 - инициальзация плейлиста для текущей даты
	* $offset = 1 - инициальзация плейлиста для следущей даты
	*/
	public function getDateNext($monitorId = '', $offset = 0){		
		$allSecond = $this->getAllSecond($monitorId, 1);	//общее время одного плейлиста в секундах
		$playlistTime = PlaylistTime::where('monitor_id', '=', $monitorId)
			->where('complete', '=', 1)
			->orderBy('dateEnd', 'desc')
			->first();

		if($playlistTime){
			$dateStart = $playlistTime->dateEnd;
		}else{
			$nowDate = Carbon::now();
			$dateStart = $nowDate->hour(0)->minute(0)->second(0)->toDateTimeString();
		}
			
		$dateStart = Carbon::parse($dateStart)->addSeconds($allSecond * $offset)->toDateTimeString();
		$dateEnd = Carbon::parse($dateStart)->addSeconds($allSecond)->toDateTimeString();

		
		$this->infoPlayist[$monitorId] = array(
			'dateStart' =>  $dateStart,
			'dateEnd'   =>  $dateEnd,
			'allSecond' =>  $allSecond,
		);

		return $this->infoPlayist;
    }
	
	
	
	/*
	* getAllSecond - общее время одного плейлиста в секундах
	* $dopTime = 1 - время с учетом заказов
	* $dopTime = 0 - время только исходных плейлистов
	*/
	public function getAllSecond($monitorId = '', $dopTime = 0){
		$allSecond = 0;
		$playlist = Playlist::select(DB::raw('SUM(playlists.time) as allTime, loop_xml'))
			->where('enable', '=', 1)
			->where('is_time', '=', 1)
			->where('monitor_id', '=', $monitorId)
			->groupBy('loop_xml')
			->get();
		if(count($playlist) > 0){
			foreach ($playlist as $key => $value){
				$allSecond += $value->allTime*($value->loop_xml + 1);
			}
		}
		if($dopTime == 1){
			$countPlaylist = ceil($allSecond/$this->timeInit);
			$dopTime = $countPlaylist*$this->timeGallery;		//Узнаем дополнительное время с учетом заказов
			$allSecond += $dopTime;
		}
		return $allSecond;
	}
	
	
	
	/*
	*	getArrAddGallery - Получение списка добавляемых заказов для данного плейлиста
	*/
	public function getArrAddGallery($monitorId = ''){
		$dateStart = $this->infoPlayist[$monitorId]['dateStart'];
		$dateEnd = $this->infoPlayist[$monitorId]['dateEnd'];
		$allSecond = $this->infoPlayist[$monitorId]['allSecond'];
		$timeOneIterPlayList = $this->timeInit + ($this->countGallery*$this->timeGallery);			//Время одного прогона плейлиста с учетом заказов
		$countIterOnePlaylist = ceil($allSecond/$timeOneIterPlayList);											//Кол-во прогонов
		$arrGallery = $this->getGalleryDateShow($monitorId, $dateEnd);									//Получение галерей которые попадут в генерируемый плейлист
		
		$arrRes = array();
		$arrTemp = array();
		for($countPlaylist = 1; $countPlaylist <=$countIterOnePlaylist; $countPlaylist++){
			$arrTemp = $this->getGalleryIterPlaylist($arrGallery, $countPlaylist);					//Получение 5 заказов за данного прогона пока не закончится плейлист
			$arrGallery = $this->countShowMinus($arrGallery, $arrTemp);								//Уменьшение кол-ва показов на 1
			$arrRes[] = $arrTemp;
		}
		
		return $arrRes;
	}
	
	
	
	/*
	* getGalleryDateShow - Получение галерей которые попадут в генерируемый плейлист
	*/
	public function getGalleryDateShow($monitorId = '', $dateEnd = ''){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		$gallery = Gallery::select(DB::raw('galleries.*, tarifs.hours, tarifs.interval_sec'))
			->join('tarifs', 'tarifs.id', '=', 'galleries.tarif_id')
			->where('status_main', '=', $status_main->id)
			->where('count_show', '>', '0')
			->where('monitor_id', '=', $monitorId)
			->where('date_show', '<=', $dateEnd)
			->orderBy('date_show', 'asc')
			->get();
			
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
		return $arrGallery;
	}
	
	
	
	/*
	* getGalleryIterPlaylist - Получение 5 заказов за данного прогона пока не закончится плейлист
	*/
	public function getGalleryIterPlaylist($arrGallery, $countPlaylist){
		$gallery = array();
		if(count($arrGallery) > 0){
			foreach($arrGallery as $key => $item){
				$sort = $this->getSort($countPlaylist, $item);
				if($sort > 0  AND $item['count_show'] > 0){
					$gallery[$item['id']]['id'] = $item['id'];
					$gallery[$item['id']]['src'] = $item['src'];
					$gallery[$item['id']]['count_show'] = $item['count_show'];
					$gallery[$item['id']]['date_show'] = $item['date_show'];
					$gallery[$item['id']]['hours'] = $item['hours'];
					$gallery[$item['id']]['interval_sec'] = $item['interval_sec'];
					$gallery[$item['id']]['monitor_id'] = $item['monitor_id'];
					
					$gallery[$item['id']]['sort'] = $sort;
					$gallery[$item['id']]['countPlaylist'] = $countPlaylist;
				}
			}
			
			$gallery = $this->array_orderby($gallery, 'countPlaylist', SORT_ASC, 'sort', SORT_DESC);
			$counter = 0;
			foreach($gallery as $key => $value){
				if($value['countPlaylist'] != $countPlaylist){
					unset($gallery[$key]);
				}else{
					$counter += 1;
					if($counter > $this->countGallery){unset($gallery[$key]);}
				}
			}
		}
		return $gallery;
	}
	
	
	
	/*
	* getSort - Вычисление коэффициента вероятности показа галлереи
	*/
	public function getSort($countPlaylist, $item){
		$sort = 0;
	
		$dateStart = $this->infoPlayist[$item['monitor_id']]['dateStart'];
		$dateShow = $item['date_show'];
		$hours = $item['hours'];
		$intervalSec = $item['interval_sec'];
		$countShow = $item['count_show'];
		
		$dateStartIter = Carbon::parse($dateStart)->addSeconds(($countPlaylist-1) * $this->timeInit);		//Узнаем дату начала прогона
		if(Carbon::parse($dateShow)->timestamp <= $dateStartIter->timestamp){									//Если дата показа меньше или равно дате начала прогона то включаем заказ
			$intervalAll = $countPlaylist * $this->timeInit;													//Узнаем для Итерации общий интервал 
			$tarifCountShow = $hours*60*60/$intervalSec;													//Узнаем сколько по тарифу должно быть показов
			
			$diffSec = Carbon::parse($dateShow)->diffInSeconds($dateStartIter);					//Узнаем разницу между датой показа и датой формируемого плейлиста
			$abstractCount = ceil($diffSec/$intervalSec);														//Узнаем сколько должно было быть показов
			$diffCount = $abstractCount - ($tarifCountShow - $countShow);							//Узнаем разницу между сколько должно быть и сколько показалось товаров
			
			$useInterval = ($tarifCountShow - $countShow + 1) * $intervalSec; 					//Узнаем используемый интервал
			$sort = $intervalAll/$useInterval * $diffCount * 100;											//Отношение общего интервала к интервалу показа и умножить коэффициент
		}

		return $sort;
	}
	
	
	
	/*
	*	countShowMinus - Уменьшение кол-ва показов на 1
	*/
	public function countShowMinus($arrGallery, $arrTemp){
		if(count($arrTemp) > 0){
			foreach($arrTemp as $key => $item){
				$newCountShow = $item['count_show'] - 1;
		
				if($newCountShow >= 0){
					$arrGallery[$item['id']]['id'] = $item['id'];
					$arrGallery[$item['id']]['src'] = $item['src'];
					$arrGallery[$item['id']]['count_show'] =	$newCountShow;		//Сохраняем новое значение
					$arrGallery[$item['id']]['date_show'] = $item['date_show'];
					$arrGallery[$item['id']]['hours'] = $item['hours'];
					$arrGallery[$item['id']]['interval_sec'] = $item['interval_sec'];
					$arrGallery[$item['id']]['monitor_id'] = $item['monitor_id'];
					$arrGallery[$item['id']]['sort'] =  $item['sort'];
					$arrGallery[$item['id']]['countPlaylist'] =  $item['countPlaylist'];
				}
			}
		}
		return $arrGallery;
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
