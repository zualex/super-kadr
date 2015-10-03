<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Image;
use Carbon\Carbon;
use SoapBox\Formatter\Formatter;
use App\Monitor;
use App\Gallery;
use App\Pay;
use App\PlaylistTime;
use App\PlaylistExtraVideo;
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
		
		
		
		$info = $this->getInfoPlaylist($this->getId(1));
		$dateStart = $info['dateStart'];
		$nowDate = Carbon::now();
		$dateNowNext = $nowDate->timestamp + $this->timePlaylist;
		while($dateNowNext >= Carbon::parse($dateStart)->timestamp){
			$playlistFinaly1 = $this->getGenerateArray(1);
			$res1 = $this->savePlaylist(1, $playlistFinaly1);
			
			$info = $this->getInfoPlaylist($this->getId(1));
			$dateStart = $info['dateStart'];
			if(count($playlistFinaly1) == 0){break;}
		}
		
		
		
		$info = $this->getInfoPlaylist($this->getId(2));
		$dateStart = $info['dateStart'];
		$nowDate = Carbon::now();
		$dateNowNext = $nowDate->timestamp + $this->timePlaylist;
		while($dateNowNext >= Carbon::parse($dateStart)->timestamp){
			$playlistFinaly2 = $this->getGenerateArray(2);
			$res2 = $this->savePlaylist(2, $playlistFinaly2);
			
			$info = $this->getInfoPlaylist($this->getId(2));
			$dateStart = $info['dateStart'];
			if(count($playlistFinaly2) == 0){break;}
			
		}
		
		
		return $res1.' - '.$res2;
	}
	
	
	
	/*
	* getGenerateArray - Получение массива для с данными для генерации плейлиста
	* offset - смещение, если 1 то масив сформируется для следующего плейлиста
	*/
	public function getGenerateArray($monitorNumber, $offset = 0){
		$res = 0;
		
		$monitorId = $this->getId($monitorNumber);					//Получение id экрана по его номеру
		$info = $this->getInfoPlaylist($monitorId, $offset);					//Получение даты начала, даты конце и idblock для генерации плейлиста
		
		$dateStart = $info['dateStart'];
		$dateEnd = $info['dateEnd'];
		$idblock = $info['idblock'];
		$maxIdblock = $info['maxIdblock'];
		$arrRes = array();
		$arrGalleryAll = $this->getGalleryDateShow($monitorId, $dateEnd);		//Получение галерей которые попадут в генерируемый плейлист	
		

		$arrTempGallery = array();
		/* Если максимальный idblock плейлиста меньше нуля то генерацим не будет */
		if($maxIdblock > 0){
			for($countNowBlock = 1; $countNowBlock <= $this->countBlock; $countNowBlock++){
				$idblock++;
				if($idblock > $maxIdblock){$idblock = 1;}

				
				/* Исходный плейлист */
				$playlist = $this->getPlaylistForGenerate($monitorId, $idblock);														//получение одного блока из исходного плейлиста
				$playlistTime =  $this->getTime($playlist);																						//Получение общего времени исходного плейлиста
				
				
				/* Заказы */
				$param = array(
					'dateStart' => $dateStart,
					'dateEnd' => $dateEnd,
					'countNowBlock' => $countNowBlock,
					'playlistTime' => $playlistTime,
				);
				$arrAddGallery = $this->getArrAddGallery($monitorId, $param, $arrGalleryAll);				//Получение списка добавляемых заказов для данного плейлиста	
				$arrGalleryAll = $this->countShowMinus($arrGalleryAll, $arrAddGallery);							//Уменьшение из общего списка кол-ва показов на 1
				$arrTempGallery[$countNowBlock] = $arrAddGallery;
				
				/* Дополнительные ролики */
				$arrDopVideo = array();
				$galleryTime = $this->getTime($arrAddGallery);
				$timeDopVideo = $this->timeBlock - $playlistTime - $galleryTime;
				if($timeDopVideo > 0 AND count($playlist) > 0){
					$PlaylistExtraVideo = PlaylistExtraVideo::all();	
					$arrDopVideo = $this->getDopVideo($timeDopVideo, $PlaylistExtraVideo);
				}
				
				$arrRes[$countNowBlock] = $this->getMergeArray($playlist, $arrAddGallery, $arrDopVideo, $countNowBlock);			//объединение исходного плейлиста с закзазами
			}
		}
		
		$res = array();
		if(count($arrRes) > 0){
			foreach($arrRes as $countNowBlock => $arr){
				foreach($arr as $key => $item){
					$res[] = $item;
				}	 
			}
		}
			

		return $res;
	}
	
	
	
	/*
	* getInfoPlaylist - Получение даты начала, даты конце и idblock для генерации плейлиста
	*
	*	dateStart 		- 	дата начала плейлиста
	*	dateEnd 		-  	дата конца плейлиста
	*	idblock 			-  	idblock плейлиста
	*	maxIdblock 	-  	максимальный idblock плейлиста
	*
	*/
	public function getInfoPlaylist($monitorId, $offset = 0){
		$res = array();
		$dateStart = '';
		$dateEnd = '';
		$idblock = 0;
		$maxIdblock = $this->getMaxIdblock($monitorId);		
		
		$playlistTime = PlaylistTime::where('monitor_id', '=', $monitorId)
			->orderBy('dateEnd', 'desc')
			->first();
			
		if($playlistTime){
			$dateStart = $playlistTime->dateEnd;		
			$idblock = $playlistTime->idblock;
		}else{
			$nowDate = Carbon::now();
			$dateStart = $nowDate->hour(0)->minute(0)->second(0)->toDateTimeString();
		}
		
		$dateStart = Carbon::parse($dateStart)->addSeconds($offset * $this->timePlaylist)->toDateTimeString();		//дата начала со смещением
		$dateEnd = Carbon::parse($dateStart)->addSeconds($this->timePlaylist)->toDateTimeString();
		

		$res = array(
			'dateStart' => $dateStart,
			'dateEnd' => $dateEnd,
			'idblock' => $idblock,
			'maxIdblock' => $maxIdblock,
		);
		
		return $res;
	}
	
	
	
	/*
	* getMaxIdblock - получение максимального idblock
	*/
	public function getMaxIdblock($monitorId){
		$res = DB::table('playlists')->max('idblock');
		if(!$res){$res = 0;}
		return $res;
	}

	
	
	/*
	*	getPlaylistForGenerate - получение одного блока из исходного плейлиста
	*/
	public function getPlaylistForGenerate($monitorId, $idblock){
		$playlist = $this
			->with('monitor')
			->where('type', '=', '0')
			->where('enable', '=', 1)
			->where('is_time', '=', 1)
			->where('monitor_id', '=', $monitorId)
			->where('idblock', '=', $idblock)
			->orderBy('sort', 'asc')
			->get();
			
		return $playlist;
	}
	

	
	
	/*
	* getArrAddGallery - Получение списка добавляемых заказов для данного плейлиста	
	*
	*	dateStart 				- 	дата начала плейлиста
	*	dateEnd 				-  	дата конца плейлиста
	*	countNowBlock 		-  	какой блок по счету
	*	playlistTime 			-  	общее время исходного плейлиста
	*
	*/
	public function getArrAddGallery($monitorId, $param, $arrGalleryAll){
		$dateStart = $param['dateStart'];
		$dateEnd = $param['dateEnd'];
		$countNowBlock = $param['countNowBlock'];
		$playlistTime = $param['playlistTime'];
		
		$arrGallery = $this->getGalleryIterPlaylist($arrGalleryAll, $dateStart, $countNowBlock, $playlistTime);		//Получение заказов для определенного логического блока
		
		return $arrGallery;
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
					"time" => $this->timeGallery,
					"loop_xml" => 0,
					"block" => 9999,
					"sort" => 0,
					"init" => 0,
				);
			}
		}	
		return $arrGallery;
	}
	
	
	
	/*
	* getTime - Получение общего времени
	*/
	public function getTime($array){
		$res = 0;
		if(count($array) > 0){
			foreach($array as $key => $item){
				$res += $item['time'] * ($item['loop_xml'] + 1);
			}
		}
		return $res;
	}
	
	
	
	/*
	* getGalleryIterPlaylist - Получение заказов для определенного логического блока
	*/
	public function getGalleryIterPlaylist($arrGallery, $dateStart, $countNowBlock, $playlistTime){
		$gallery = array();
		foreach($arrGallery as $key => $item){
			$sort = $this->getSort($item, $dateStart, $countNowBlock, $playlistTime);
			if($sort > 0  AND $item['count_show'] > 0){
				$gallery[$item['id']]['id'] = $item['id'];
				$gallery[$item['id']]['src'] = $item['src'];
				$gallery[$item['id']]['count_show'] = $item['count_show'];
				$gallery[$item['id']]['date_show'] = $item['date_show'];
				$gallery[$item['id']]['hours'] = $item['hours'];
				$gallery[$item['id']]['interval_sec'] = $item['interval_sec'];
				$gallery[$item['id']]['monitor_id'] = $item['monitor_id'];
				$gallery[$item['id']]['tarif_id'] = $item['tarif_id'];
				$gallery[$item['id']]['time'] = $item['time'];
				$gallery[$item['id']]['loop_xml'] = $item['loop_xml'];
				$gallery[$item['id']]['block'] = $countNowBlock;
				
				$gallery[$item['id']]['sort'] = $sort;
				$gallery[$item['id']]['init'] = 0;
			}
		}		
		
		$gallery = $this->array_orderby($gallery, 'sort', SORT_DESC);
		$galleryTime = 0;
		foreach($gallery as $key => $item){
			$galleryTime += $item['time'] * ($item['loop_xml'] + 1);
			if($galleryTime > $this->timeBlock - $playlistTime){
				unset($gallery[$key]);
			}
		}
		return $gallery;
	}
	
	
	

	/*
	* getSort - Вычисление коэффициента вероятности показа галлереи
	*/
	public function getSort($item, $dateStart, $countNowBlock, $playlistTime){
		$sort = 0;
		
		$dateShow = $item['date_show'];
		$hours = $item['hours'];
		$intervalSec = $item['interval_sec'];
		$countShow = $item['count_show'];
		
		$dateStartIter = Carbon::parse($dateStart)->addSeconds($playlistTime);				//Узнаем дату начала прогона
		if(Carbon::parse($dateShow)->timestamp <= $dateStartIter->timestamp){			//Если дата показа меньше или равно дате начала прогона то включаем заказ
			$intervalAll = $countNowBlock * $this->timeBlock;												//Узнаем для Итерации общий интервал 
			$tarifCountShow = $hours*60*60/$intervalSec;													//Узнаем сколько по тарифу должно быть показов
			
			$diffSec = Carbon::parse($dateShow)->diffInSeconds($dateStartIter);					//Узнаем разницу между датой показа и датой формируемого плейлиста
			$abstractCount = ceil($diffSec/$intervalSec);														//Узнаем сколько должно было быть показов
			$diffCount = $abstractCount - ($tarifCountShow - $countShow);							//Узнаем разницу между сколько должно быть и сколько показалось товаров

			$useInterval = ($tarifCountShow - $countShow + 1) * $intervalSec; 					//Узнаем используемый интервал
			$sort = ($intervalAll/$useInterval) * ($diffCount * 100);	
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
					$arrGallery[$item['id']]['tarif_id'] = $item['tarif_id'];
					$arrGallery[$item['id']]['time'] =  $item['time'];
					$arrGallery[$item['id']]['loop_xml'] =  $item['loop_xml'];
					$arrGallery[$item['id']]['block'] =  $item['block'];
					$arrGallery[$item['id']]['sort'] =  $item['sort'];
					$arrGallery[$item['id']]['init'] =  $item['init'];
				}
			}
		}
		return $arrGallery;
	}

	
	
	/*
	* getDopVideo - Получение списко дополнительных роликов
	*/
	public function getDopVideo($timeDopVideo, $PlaylistExtraVideo){
		$arrRes = array();
		$timeLeft = $timeDopVideo;
				
		if(count($PlaylistExtraVideo) > 0){
			while($timeLeft !== false){
				foreach($PlaylistExtraVideo as $key => $item){
					$path = $item->path;
					$time = $item->time;
					if($timeLeft - $time > 0){
						$timeLeft -= $time;
					}else{
						$time = $timeLeft;		//Обрезаем ролик если нет оставшегося времени
						$timeLeft = false;				//Условие выхода из цикла
					}
					
					if($time !== false AND $time > 0){
						$arrRes[] = array(
							'id' => $item->id,
							'path' => $path,
							'time' => $time,
						);
					}
	
					if($timeLeft == $timeDopVideo){	//Если оставшееся время не изменилось то выходим из цикла
						$timeLeft = false;						//Условие выхода из цикла
					}
				}
			}
		}

		return $arrRes;
	}

	
	
	
	/*
	* getMergeArray - объединение исходного плейлиста с закзазами
	*/
	public function getMergeArray($playlist, $arrAddGallery, $arrDopVideo, $countNowBlock){
		$arrRes = array();
		if(count($playlist) > 0){
			foreach($playlist as $key => $item){
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
				
				$sort = 1000 - $key;		//обратная сортровка для коректной сортроваки объединенного массива
				$arrRes[] = array(
					'id' => $item['id'],
					'enable' => $item['enable'],
					'name' => $item['name'],
					'loop' => $item['loop_xml'],
					'loop_xml' => $item['loop_xml'],
					'IsTime' => $item['is_time'],
					'time' => $item['time'],
					'sort' => $sort,
					'block' => $countNowBlock,
					'init' => 1
				);		
			}
		}
		
		if(count($arrAddGallery) > 0){
			foreach($arrAddGallery as $key => $item){	
				$arrRes[] = array(
					'id' => $item['id'],
					'enable' => 'True',
					'name' => $item['src'],
					'loop' => 0,
					'loop_xml' => 0,
					'IsTime' => 'True',
					'time' => $this->timeGallery,
					'sort' => $item['sort'],
					'block' => $countNowBlock,
					'init' => 0
				);	
			}	
		}
		
		if(count($arrDopVideo) > 0){
			foreach($arrDopVideo as $key => $item){
				$sort = 1000 - $key;					//обратная сортровка для коректной сортроваки объединенного массива
				$arrRes[] = array(
					'id' => 'video_'.$item['id'],
					'enable' => 'True',
					'name' => $item['path'],
					'loop' => 0,
					'loop_xml' => 0,
					'IsTime' => 'True',
					'time' => $item['time'],
					'sort' => $sort ,
					'block' => $countNowBlock,
					'init' => -1
				);
			}
		}
		

		$arrRes = $this->array_orderby($arrRes, 'block', SORT_ASC, 'init', SORT_DESC, 'sort', SORT_DESC);
		
		return $arrRes;
	}
	
	
	
	/*
	* savePlaylist - сохранение плейлиста
	*/
	public function savePlaylist($monitorNumber, $playlistFinaly, $offset = 0){
		$res = 0;
		
		$monitorId = $this->getId($monitorNumber);					//Получение id экрана по его номеру
		$info = $this->getInfoPlaylist($monitorId, $offset);					//Получение даты начала, даты конце и idblock для генерации плейлиста
		$dateStart = $info['dateStart'];
		$dateEnd = $info['dateEnd'];
		$idblock = $info['idblock'];
		$maxIdblock = $info['maxIdblock'];
		
		$lastIdblock = $idblock + $this->countBlock;
		if($lastIdblock > $maxIdblock){$lastIdblock -= $maxIdblock;}

		
		
		if(count($playlistFinaly) > 0){
			$res = 1;
			$this->savePlaylistWithGalleryXml($monitorNumber, $playlistFinaly, $dateStart);		//Сохранение плейлиста в xml
			$this->setGalleryCountShow($playlistFinaly);															//Обновление в заказах CountShow
			$this->setPlaylistTime($monitorId, $dateStart, $dateEnd, $lastIdblock);										//Сохранение в базу данных инф. о плейлистах
		}
		
		
		return $res;
	}
	
	
	
	/*
	*	savePlaylistWithGalleryXml - Сохранение плейлиста
	*/
	public function savePlaylistWithGalleryXml($monitorNumber = '', $arrRes = array(), $dateStart){
		if($monitorNumber != '' AND count($arrRes) > 0){			
			$dateStart = Carbon::parse($dateStart);
			$namePlaylist = 'ПЛ'.$dateStart->format('YmdHis').'.xml';
			$namePlaylist = iconv("UTF-8", "cp1251", $namePlaylist);
			
			if($monitorNumber == 1){
				$pathSave = $this->pathPlaylistMonitor_1.'/'.$namePlaylist;
			}
			if($monitorNumber == 2){
				$pathSave = $this->pathPlaylistMonitor_2.'/'.$namePlaylist;
			}
			$this->clearFolderBeforeGeneration($monitorNumber);		//Очщение старых плейлистов и папки images перед генерацией новых плейлистов
			
			
			$xml = '';
			
			$xml .= '<?xml version="1.0" encoding="windows-1251"?>
<!--Nata-Info Ltd. NISheduler.Sheduler playlist-->
<tasks>
	<collection base="C:\Ролики\Ролики\">';
			
			foreach($arrRes as $key => $item){
				if($item['init'] == 0){
					$item['name'] = $this->savePlaylistImg($monitorNumber, $item);		//сохранение картинки для плейлиста		
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
	public function clearFolderBeforeGeneration($monitorNumber){
		if($monitorNumber == 1){
			$path = $this->pathPlaylistMonitor_1;
		}
		if($monitorNumber == 2){
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
	public function savePlaylistImg($monitorNumber, $item){
		$pathStart = $this->pathImages.'/o_'.$item['name'];
		$pathSave = '';
		if($monitorNumber == 1){
			$pathSave = $this->pathPlaylistMonitor_1.'/'.$this->folderImg.'/'.$item['name'];
		}
		if($monitorNumber == 2){
			$pathSave = $this->pathPlaylistMonitor_2.'/'.$this->folderImg.'/'.$item['name'];
		}
		$w = $this->imgSize[$monitorNumber]['w'];
		$h = $this->imgSize[$monitorNumber]['h'];
		
		if(!File::exists($pathSave)){
			Image::make($pathStart)->resize($w, $h)->save($pathSave);
		}
		
		
		$pathSave = str_replace('/', '\\', $pathSave);
		return $pathSave;
	}
	
	
	
	/*
	*	setGalleryCountShow - Обновление в заказах CountShow
	*/
	public function setGalleryCountShow($arrRes){
		if(count($arrRes) > 0){
			foreach($arrRes as $key => $item){
				if($item['init'] == 0){
					$gallery = Gallery::find($item['id']);
					if($gallery){
						$newCount = $gallery->count_show - 1;
						if($newCount < 0){$newCount = 0;}
						$gallery->count_show = $newCount;
						$gallery->save();
					}
				}
			}
		}
		return 1;
	}
	
	
	
	/*
	* setPlaylistTime - Сохранение в базу данных инф. о плейлистах
	*/
	public function setPlaylistTime($monitorId = '', $dateStart = '', $dateEnd = '', $idblock){
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
				$playlistTime->idblock = $idblock;
				$playlistTime->save();
			}
		}
		return $playlistTime;
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
