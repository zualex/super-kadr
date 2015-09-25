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

	public $error;
	public $pathPlaylistMonitor_1;	//плейлисты для Экрана 1
	public $pathPlaylistMonitor_2;	//плейлисты для Экрана 2
	public $folderInit;						//папка исходного плейлиста
	public $folderImg;					//путь к картинкам
	public $imgSize;						//размеры для плейлистов
	
	public $pathImages;					//путь к оригинальным картинкам

	
	public $timeInit;						//5 минут из исходного плейлиста
	public $countGallery;				//5 показов наших заказов
	public $timeGallery;					//5 секунд показ заказов
	public $infoPlayist;					//Информация о генерируемых плейлистах

	
	
	public function __construct(){
		$this->error = array();
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
	* getInitPlaylistByMonitor - Получение исходного плейлиста из БД выборка по экрану
	*/
	public function getInitPlaylistByMonitor($monitorId){
		$playlist = $this
			->with('monitor')
			->where('type', '=', '0')
			->where('enable', '=', 1)
			->where('is_time', '=', 1)
			->where('monitor_id', '=', $monitorId)
			->orderBy('sort', 'asc')
			->get();
			
		//Если дается мало времени на плейлист то ограничиваем	
		if($playlist){
			$allSecond = $this->infoPlayist[$monitorId]['allSecond'];
			$allSecondAllPlaylist = $this->getAllSecond($monitorId, 0);
			if($allSecondAllPlaylist != $allSecond){				//Ели общее время плейлистов не совпадает со временем которое дается на плейлист
				$checkSecond = 0;
				foreach($playlist as $key => $item){
					$checkSecond += $item['time'] * ($item['loop_xml'] + 1);
					if($checkSecond <= $allSecond){
						$playlist[$key] = $item;
					}else{
						unset($playlist[$key] );
					}
				}
			}
		}
	
		return $playlist;
	}
	
	
	
	/*
	* initFile - загрузка исходных файлов в базу данных
	*/
	public function initFile(){		
		$Monitor_1 = Monitor::where('number', '=', 1)->first();
		$Monitor_2 = Monitor::where('number', '=', 2)->first();
		$check1 = 0;
		$check2 = 0;
		
		//Генерация плейлистов
		
		$this->getDateNext($Monitor_1->id);									//Формирование в $this->infoPlayist информации следующего плейлиста
		$nowDate = Carbon::now();
		$dateNowNext = $nowDate->timestamp + $this->infoPlayist[$Monitor_1->id]['allSecond'];
		while($dateNowNext >= Carbon::parse($this->infoPlayist[$Monitor_1->id]['dateStart'])->timestamp){
			$check1 = $this->generationNewPlay($Monitor_1->id);				//Генерация плейлистов
			if($check1 == 0){break;}
		}
		
		
		$this->getDateNext($Monitor_2->id);									//Формирование в $this->infoPlayist информации следующего плейлиста
		$nowDate = Carbon::now();
		$dateNowNext = $nowDate->timestamp + $this->infoPlayist[$Monitor_2->id]['allSecond'];
		while($dateNowNext >= Carbon::parse($this->infoPlayist[$Monitor_2->id]['dateStart'])->timestamp){
			$check2 = $this->generationNewPlay($Monitor_2->id);				//Генерация плейлистов
			if($check2 == 0){break;}
		}

		return $check1." - ".$check2 ;
	}
	
	
	
	/*
	* generationNewPlay - генерация файла плейлиста
	*/
	public function generationNewPlay($monitorId = ''){
		$res = 0;
		$checkInit = $this->checkGenerateInitPlaylist($monitorId);			//Проверка нужно ли сохранение исходного плейлиста в базу данных
		
		$playlist = $this->getInitPlaylistByMonitor($monitorId);				//Получение исходного плейлиста	
		$arrAddGallery = $this->getArrAddGallery($monitorId);				//Получение списка добавляемых заказов для данного плейлиста		
		$arrRes = $this->getMergeArray($playlist, $arrAddGallery);			//объединение исходного плейлиста с закзазами
		
		if(count($arrRes) > 0){
			$res = 1;
			$this->infoPlayist[$monitorId]['dateEnd'] = $this->setDateEnd($monitorId, $arrRes);		//Обновление dateEnd
			$this->savePlaylistWithGalleryXml($monitorId, $arrRes);				//Сохранение плейлиста в xml
			$this->setGalleryCountShow($arrAddGallery);								//Обновление в заказах CountShow
			$this->setPlaylistTime($monitorId, $this->infoPlayist[$monitorId]['dateStart'], $this->infoPlayist[$monitorId]['dateEnd']);		//Сохранение в базу данных инф. о плейлистах
		}
		
		$this->getDateNext($monitorId);			//Обновление информации
		

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
		$allSecond = $this->getAllSecond($monitorId, 0);	//общее время одного плейлиста в секундах
		$playlistTime = PlaylistTime::where('monitor_id', '=', $monitorId)
			->orderBy('dateEnd', 'desc')
			->first();

		if($playlistTime){
			$dateStart = $playlistTime->dateEnd;
		}else{
			$nowDate = Carbon::now();
			$dateStart = $nowDate->hour(0)->minute(0)->second(0)->toDateTimeString();
		}
			
		$dateStart = Carbon::parse($dateStart)->addSeconds($allSecond * $offset);
		$dateEnd = Carbon::parse($dateStart)->addSeconds($allSecond);
		
		//Если конец генерации попадает на конец нового день
		if($dateStart->day != $dateEnd->day){
			$dateEnd = $dateEnd->hour(0)->minute(0)->second(0);
			$allSecond =  $dateEnd->diffInSeconds($dateStart);
		}

		$dateStart = $dateStart->toDateTimeString();
		$dateEnd = $dateEnd->toDateTimeString();
		
		
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
		$allSecond = $this->infoPlayist[$monitorId]['allSecond'];			//Время плейлиста	
		$allSecondAllPlaylist = $this->getAllSecond($monitorId, 0);			//Общее время плейлиста
		$arrRes = array();

		//если не совпадает то не нужно включать заказы, так как время плейлиста жестко ограничено $allSecond
		if($allSecond == $allSecondAllPlaylist){
			$countIterOnePlaylist = ceil($allSecond/$this->timeInit);												//Кол-во прогонов
			$arrGallery = $this->getGalleryDateShow($monitorId, $dateEnd);									//Получение галерей которые попадут в генерируемый плейлист		
			$arrTemp = array();
			for($countPlaylist = 1; $countPlaylist <=$countIterOnePlaylist; $countPlaylist++){
				$arrTemp = $this->getGalleryIterPlaylist($arrGallery, $countPlaylist);					//Получение 5 заказов за данного прогона пока не закончится плейлист
				$arrGallery = $this->countShowMinus($arrGallery, $arrTemp);								//Уменьшение кол-ва показов на 1
				$arrRes[] = $arrTemp;
			}
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
					"tarif_id" => $item->tarif_id,
					"sort" => 0,
					"countPlaylist" => 9999,
					"init" => 0,
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
					$gallery[$item['id']]['tarif_id'] = $item['tarif_id'];
					
					$gallery[$item['id']]['sort'] = $sort;
					$gallery[$item['id']]['countPlaylist'] = $countPlaylist;
					$gallery[$item['id']]['init'] = 0;
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
			$diffCount = $abstractCount - ($tarifCountShow - $countShow) + 1;							//Узнаем разницу между сколько должно быть и сколько показалось товаров
			
			$useInterval = ($tarifCountShow - $countShow + 1) * $intervalSec; 					//Узнаем используемый интервал
			//$sort = $intervalAll/$useInterval * $diffCount * 100;											//Отношение общего интервала к интервалу показа и умножить коэффициент
			$sort = ($intervalAll/$useInterval) * ($diffCount * 100);										
		}
		
		//if($countPlaylist == 4 AND $item['id'] == 3){
		//	dd($useInterval);
		//}
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
					$arrGallery[$item['id']]['tarif_id'] = $item['tarif_id'];
					$arrGallery[$item['id']]['sort'] =  $item['sort'];
					$arrGallery[$item['id']]['countPlaylist'] =  $item['countPlaylist'];
					$arrGallery[$item['id']]['init'] =  $item['init'];
				}
			}
		}
		return $arrGallery;
	}
	
	
	
	/*
	* getMergeArray - объединение исходного плейлиста с закзазами
	*/
	public function getMergeArray($playlist, $arrAddGallery){
		$arrRes = array();
		$timePlaylist = 0;
		if(count($playlist) > 0){
			foreach($playlist as $key => $item){
				$timePlaylist += $item['time'] * ($item['loop_xml'] + 1);
				$ratio = floor($timePlaylist / $this->timeInit) + 1;
				if($item['enable'] == 1){
					$item['enable'] = 'True';
				}else{
					$item['enable'] = 'False';
				}
				if($item['is_time'] == 1){
					$item['is_time'] = 'True';
				}else{
					$item['is_time'] = 'False';
				}
							
				$arrRes[] = array(
					'id' => $item['id'],
					'enable' => $item['enable'],
					'name' => $item['name'],
					'loop' => $item['loop_xml'],
					'IsTime' => $item['is_time'],
					'time' => $item['time'],
					'sort' => $key,
					'countPlaylist' => $ratio,
					'init' => 1
				);		
			}
		}
		
		if(count($arrAddGallery) > 0){
			foreach($arrAddGallery as $key1 => $arrItem){
				foreach($arrItem as $key2 => $item){	
					$arrRes[] = array(
						'id' => $item['id'],
						'enable' => 'True',
						'name' => $item['src'],
						'loop' => 0,
						'IsTime' => 'True',
						'time' => $this->timeGallery,
						'sort' => $item['sort'],
						'countPlaylist' => $item['countPlaylist'],
						'init' => 0
					);	
				}	
			}
		}
		$arrRes = $this->array_orderby($arrRes, 'countPlaylist', SORT_ASC, 'init', SORT_DESC, 'sort', SORT_DESC);
		
		return $arrRes;
	}

	
	
	/*
	*	getTimePlaylist - Получение общего времени
	*/
	public function getTimePlaylist($arrRes){
		$timePlaylist = 0;
		if(count($arrRes) > 0){
			foreach($arrRes as $key => $item){
				$timePlaylist += $item['time'] * ($item['loop'] + 1);
			}
		}
		return $timePlaylist;
	}

		
	
	/*
	*	savePlaylistWithGalleryXml - Сохранение плейлиста
	*/
	public function savePlaylistWithGalleryXml($monitorId = '', $arrRes = array()){
		if($monitorId != '' AND count($arrRes) > 0){
			$dateStart = $this->infoPlayist[$monitorId]['dateStart'];
			$dateEnd = $this->infoPlayist[$monitorId]['dateEnd'];
			
			$dateStart = Carbon::parse($dateStart);
			$namePlaylist = 'ПЛ'.$dateStart->format('YmdHis').'.xml';
			$namePlaylist = iconv("UTF-8", "cp1251", $namePlaylist);
			
			if($this->getNumber($monitorId) == 1){
				$pathSave = $this->pathPlaylistMonitor_1.'/'.$namePlaylist;
			}
			if($this->getNumber($monitorId) == 2){
				$pathSave = $this->pathPlaylistMonitor_2.'/'.$namePlaylist;
			}
			
			
			$this->clearFolderBeforeGeneration($monitorId);		//Очщение старых плейлистов и папки images перед генерацией новых плейлистов
			
			
			$xml = '';
			
			$xml .= '<?xml version="1.0" encoding="windows-1251"?>
<!--Nata-Info Ltd. NISheduler.Sheduler playlist-->
<tasks>
	<collection base="C:\Ролики\Ролики\">';
			
			foreach($arrRes as $key => $item){
				if($item['init'] == 0){
					$item['name'] = $this->savePlaylistImg($monitorId, $item);		//сохранение картинки для плейлиста		
				}
				
				
				$xml .= '<item enable="'.$item['enable'].'" name="'.$item['name'].'" loop="'.$item['loop'].'" IsTime="'.$item['IsTime'].'" time="'.$item['time'].'"></item>
';				
			}
	
			$xml .= '	</collection>
</tasks>';
			
			
			

			$xml = iconv("UTF-8", "cp1251", $xml);
			File::put($pathSave, $xml);
			
			return $pathSave;
		}
	}
	
	
	
	/*
	* Очщение старых плейлистов и папки images перед генерацией новых плейлистов
	*/
	public function clearFolderBeforeGeneration($monitorId){
		if($this->getNumber($monitorId) == 1){
			$path = $this->pathPlaylistMonitor_1;
		}
		if($this->getNumber($monitorId) == 2){
			$path = $this->pathPlaylistMonitor_2;
		}
		
		$files = File::files($path);
		File::delete($files);
		
		$images = File::files($path.'/'.$this->folderImg);
		File::delete($images);
		
		return 1;		
	}
	
	
	/*
	* savePlaylistImg - сохранение картинки для плейлиста
	*/
	public function savePlaylistImg($monitorId, $item){
		$pathStart = $this->pathImages.'/o_'.$item['name'];
		$pathSave = '';
		if($this->getNumber($monitorId) == 1){
			$pathSave = $this->pathPlaylistMonitor_1.'/'.$this->folderImg.'/'.$item['name'];
		}
		if($this->getNumber($monitorId) == 2){
			$pathSave = $this->pathPlaylistMonitor_2.'/'.$this->folderImg.'/'.$item['name'];
		}
		$w = $this->imgSize[$monitorId]['w'];
		$h = $this->imgSize[$monitorId]['h'];
		
		if(!File::exists($pathSave)){
			Image::make($pathStart)->resize($w, $h)->save($pathSave);
		}
		
		
		$pathSave = str_replace('/', '\\', $pathSave);
		return $pathSave;
	}
		
	
	
	/*
	*	setGalleryCountShow - Обновление в заказах CountShow
	*/
	public function setGalleryCountShow($arrAddGallery){
		$arrRes = array();
		if(count($arrAddGallery) > 0){
			foreach($arrAddGallery as $key1 => $arrItem){
				foreach($arrItem as $key2 => $item){
					if (array_key_exists($item['id'], $arrRes)) {
						$arrRes[$item['id']] += 1;
					}else{
						$arrRes[$item['id']] = 1;
					}
				}
			}
		}
		
		if(count($arrRes) > 0){
			foreach($arrRes as $id => $count){
				$gallery = Gallery::find($id);
				if($gallery){
					$newCount = $gallery->count_show - $count;
					if($newCount < 0){$newCount = 0;}
					$gallery->count_show = $newCount;
					$gallery->save();
				}
			}
		}
		return 1;
	}
	
	
	
	/*
	* setPlaylistTime - Сохранение в базу данных инф. о плейлистах
	*/
	public function setPlaylistTime($monitorId = '', $dateStart = '', $dateEnd = ''){
		$playlistTime = false;
		if($monitorId != '' AND $dateStart != '' AND $dateEnd != ''){
			//Проверям есть ли в базе данная запись
			$playlistTimeExist = PlaylistTime::where('monitor_id', '=', $monitorId)
				->where('dateStart', '=', $dateStart)
				->where('dateEnd', '=', $dateEnd)
				->first();
			if(!$playlistTimeExist){
				$playlistTime = new PlaylistTime;
				$playlistTime->monitor_id = $monitorId;
				$playlistTime->dateStart = $dateStart;
				$playlistTime->dateEnd = $dateEnd;
				$playlistTime->save();
			}
		}
		return $playlistTime;
	}
		
	
		
	/*
	*	checkGenerateInitPlaylist - Проверка нужно ли сохранение исходного плейлиста в базу данных
	*/
	public function checkGenerateInitPlaylist($monitorId){
		$res = 0;
		$checkDate = Carbon::parse($this->infoPlayist[$monitorId]['dateStart']);

		if($checkDate->hour == 0 AND $checkDate->minute == 0 AND $checkDate->second == 0){
			$day = sprintf("%02d", $checkDate->day);
			$month = sprintf("%02d", $checkDate->month);
			$nameInitFile = 'ПЛ'.$day.$month.$checkDate->year.'.xjob';
			$nameInitFile = iconv("UTF-8", "cp1251", $nameInitFile);
			

			if($this->getNumber($monitorId) == 1){
				$path = $this->pathPlaylistMonitor_1.'/'.$this->folderInit;
			}
			if($this->getNumber($monitorId) == 2){
				$path = $this->pathPlaylistMonitor_2.'/'.$this->folderInit;
			}
			
			$pathFileInit = $path.'/'.$nameInitFile;
			if (File::exists($pathFileInit)){
				$this->deleteInitPlaylist($monitorId);
				$this->saveFileInDB($pathFileInit, $monitorId);
			}


			foreach(File::files($path) as $key => $file){
				if($pathFileInit != $file){
					File::delete($file);
				}
				
			}
			$res = 1;
		}
		return $res;
	}
	
	
	
	/*
	* setDateEnd - Обновление dateEnd
	*/
	public function setDateEnd($monitorId, $arrRes){
		$dateStart = $this->infoPlayist[$monitorId]['dateStart'];
		$dateEnd = $this->infoPlayist[$monitorId]['dateEnd'];
		if(count($arrRes) > 0){
			$timePlaylist = $this->getTimePlaylist($arrRes);			//Получение общего времени			
			$dateEnd = Carbon::parse($dateStart)->addSeconds($timePlaylist);
			//для того чтобы не сбилась генерация и для того чтобы слишком маленький файл плейлиста не формировался
			if($dateEnd->hour == '23' AND $dateEnd->minute >= '57'){
				$newDay = $dateEnd->day + 1;
				$dateEnd = $dateEnd->day($newDay)->hour(0)->minute(0)->second(0);
			}
			
			//для того чтобы не сбилась генерация
			if($dateEnd->day != Carbon::parse($dateStart)->day){
				$dateEnd = $dateEnd->hour(0)->minute(0)->second(0);
			}
			
			$dateEnd = $dateEnd->toDateTimeString();
		}
		return $dateEnd;
	}
			
		
	
	
	/*
	* Получение номера экрана по id
	*/
	public function getNumber($monitorId) {
		$res = '';
		$Monitor_1 = Monitor::where('id', '=', $monitorId)->first();
		if($Monitor_1){
			$res = $Monitor_1->number;
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
