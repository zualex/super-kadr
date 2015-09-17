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
	* Если заказ был в предыдущей пятиминтку то коэффициенты равны по умолчанию.
	* Для того чтобы определить попадал товар в предыдущую пятимитку или нет для этого есть count_show, который показывает сколько показов осталось
	* Например если заказ с датаой показа 12:00 c тарифом 2 (4 показа в час в течение 5 часов. - каждые 15 мин. - всего count_show = 20)
	* сейчас время например 12:00, count_show = 20, то коэффициент попадания = 1%
	* сейчас время например 12:05, count_show = 20, то коэффициент попадания = 33%
	* сейчас время например 12:10, count_show = 20, то коэффициент попадания = 66%
	* сейчас время например 12:15, count_show = 20, то коэффициент попадания = 100%
	* сейчас время например 12:20, count_show = 20, то коэффициент попадания = 133%
	* сейчас время например 12:25, count_show = 19, то коэффициент попадания = 66%
	* сейчас время например 12:30, count_show = 18, то коэффициент попадания = 1%
	* сейчас время например 12:35, count_show = 18, то коэффициент попадания = 33%
	*
	*
	*/
	public function getGalleryGeneration($monitorId = ''){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		
		$gallery = Gallery::select(DB::raw('galleries.*, tarifs.hours, tarifs.interval_sec'))
			->join('tarifs', 'tarifs.id', '=', 'galleries.tarif_id')
			->where('status_main', '=', $status_main->id)
			->where('count_show', '>', '0')
			->where('monitor_id', '=', $monitorId)
			->where('date_show', '<=', $this->dateEnd)
			->orderBy('date_show', 'asc')
			->get();
			
		$arrGallery = array();
		if(count($gallery) > 0){
			foreach($gallery as $key => $item){				
			
				$tarifCountShow = $item->hours*60*60/$item->interval_sec;								//Узнаем сколько по тарифу должно быть показов
				$secondAdd = $tarifCountShow*$item->interval_sec;											//Узнаем через сколько секунд тариф закончится
				$dataEndTarif = Carbon::parse($item->date_show)->addSeconds($secondAdd);	//Узнаем дату конца тарифа
				
				//Если текущая дата больше чем дата конца тарифа то $ost_sec = 0.01
				//Если текущая дата меньше чем дата конца тарифа то проблем нет
				if(Carbon::now()->timestamp >= $dataEndTarif->timestamp){
					$ost_sec = 0.01;
				}else{
					$ost_sec = Carbon::now()->diffInSeconds($dataEndTarif);		//разница между текущим временем и концом даты тарифа
				}
				
				
				/*
				* Формула:
				* Td/Ir * It/Ir, где Td - 5 минут из исходного плейлиста
				*							Ir - реальный интервал = кол-во оставшихся секунд до конца тарифа / кол-во оставшихся показов		
				*							It - интервал по тарифу = кол-во оставшихся секунд до конца тарифа / кол-во показов из тарифа
				*/
				$real_interval = $ost_sec/$item->count_show;
				$sort = 300/$real_interval*($item->interval_sec/$real_interval)*100;
				
				
				
				if($key == 19){
					//dd($item->interval_sec);
				}
				

				
				$arrGallery[$item->id] = array(
					"id" => $item->id,
					"src" => $item->src,
					"count_show" => $item->count_show,
					"date_show" => $item->date_show,
					"hours" => $item->hours,
					"interval_sec" => $item->interval_sec,
					"sort" => $sort,
				);
			}
		}
		dd($arrGallery);
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
			'2' => 20,
			'3' => 48 
		);

		for($i = 1; $i <= 50; $i++){
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
