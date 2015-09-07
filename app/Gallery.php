<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Hash;
use Session;
use Auth;
use Image;


use App\Status;
use App\Monitor;
use App\Tarif;
use App\Like;
class Gallery extends Model {
	
	public $error;
	public $pathImages;
	
	public function __construct(){
		$this->error = array();
		$this->pathImages = "/public/images";
	}

	
	public function likes()
    {
        return $this->hasMany('App\Like');
    }
	
	public function comments()
    {
        return $this->hasMany('App\Comment');
    }
	
	/*
	* mainGallery - вывод на главной
	*/
	public function mainGallery(){
		$gallery = false;
		
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		if(count($status_main) == 0){$this->error[] = 'Не найден статус Main';}
		
		if(count($this->error) == 0){
			$gallery = $this
				->with('likes')
				->with('comments')
				->where('status_main', '=', $status_main->id)
				->take(15)
				->get();

			$gallery->pathImages = $this->pathImages;			
		}
		
		return $gallery;
	}
	
	
	/*
	* getGallery - вывод одной галереи
	*/
	public function getGallery($id){
		$gallery = false;
		
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		if(count($status_main) == 0){$this->error[] = 'Не найден статус Main';}
		
		if(count($this->error) == 0){
			$gallery =$this
				->where('id', '=', $id)
				->with('likes')
				->with('comments')
				->first();
			$gallery->pathImages = $this->pathImages;
		}
		
		return $gallery;
	}
			
	

	
	
	
	/*
	* Создание галереи
	*	'monitor'
	*	'image'
	*/
	public function createGallery($param){
		$gallery = false;
		if(!array_key_exists('monitor', $param)){$param['monitor'] = '';}
		if(!array_key_exists('image', $param)){$param['image'] = '';}
		
		if($param['monitor'] == ''){$this->error[] = 'Не выбран экран';}
		if($param['image'] == ''){$this->error[] = 'Не загружено фото';}
		if(!Auth::check()){$this->error[] = 'Необходимо авторизоваться';}
		
		//Проверка наличия файла	
		if(count($this->error) == 0){
			$dir = $this->pathImages . "/temp/".Auth::user()->id;
			$uploadImage = array_diff(scandir(base_path().$dir), array('..', '.'));
			$uploadImage = array_shift($uploadImage);
			if(!$uploadImage){$this->error[] = 'Не загружено фото';}
		}
		
		if(count($this->error) == 0){
			$sizeImg = Monitor::find($param['monitor']);
			$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'moderation')->first();
			$status_order = Status::where('type_status', '=', 'order')->where('caption', '=', 'process')->first();
			$path_parts = pathinfo(base_path().$dir.'/'.$uploadImage);
			$ext = '.'.$path_parts['extension'];
			
			if(count($sizeImg) == 0){$this->error[] = 'Не найден экран';}
			
			if(count($status_main) == 0){$this->error[] = 'Не найден статус Main';}
			if(count($status_order) == 0){$this->error[] = 'Не найден статус Order';}
		}
		
		
		if(count($this->error) == 0){
			$gallery = new Gallery;
			$gallery->user_id = Auth::user()->id;
			$gallery->status_main = $status_main->id;
			$gallery->status_order = $status_order->id;
			$gallery->status_order = $status_order->id;
			$gallery->save();
			
			$galleryId = $gallery->id;
			$src = $galleryId.$ext;
			
			//Update Gallery (update src)
			$gallery->src = $src;
			$gallery->save();
			
			
			
			Image::make(base_path().$dir.'/'.$uploadImage)->save(base_path().$this->pathImages.'/o_'.$src);
			Image::make(base_path().$dir.'/'.$uploadImage)->resize($sizeImg['mediumWidth'], $sizeImg['mediumHeight'])->save(base_path().$this->pathImages.'/m_'.$src);
			Image::make(base_path().$dir.'/'.$uploadImage)->resize($sizeImg['smallWidth'], $sizeImg['smallHeight'])->save(base_path().$this->pathImages.'/s_'.$src);
			
		}
		
		return $gallery;
	}
	
	
	public function dateContent(){
		$day_of_week = date('N');
		function day_of_week($day) {
			switch ($day) {
				case ($day == 1 || $day == 8 || $day == 15):
					$return = "Понедельник";
					break;
				case ($day == 2 || $day == 9 || $day == 16):
					$return = "Вторник";
					break;
				case ($day == 3 || $day == 10 || $day == 17):
					$return = "Среда";
					break;
				case ($day == 4 || $day == 11 || $day == 18):
					$return = "Четверг";
					break;
				case ($day == 5 || $day == 12 || $day == 19):
					$return = "Пятница";
					break;
				case ($day == 6 || $day == 13 || $day == 20):
					$return = "Суббота";
					break;
				case ($day == 7 || $day == 14 || $day == 21):
					$return = "Воскресенье";
					break;		
			}
			return $return;
		}
		function month ($month) {
			switch ($month) {
				case 1:
					$return = "Января";
					break;
				case 2:
					$return = "Февраля";
					break;
				case 3:
					$return = "Марта";
					break;
				case 4:
					$return = "Апреля";
					break;
				case 5:
					$return = "Мая";
					break;
				case 6:
					$return = "Июня";
					break;
				case 7:
					$return = "Июля";
					break;
				case 8:
					$return = "Августа";
					break;	
				case 9:
					$return = "Сентября";
					break;	
				case 10:
					$return = "Октября";
					break;	
				case 11:
					$return = "Ноября";
					break;	
				case 12:
					$return = "Декабря";
					break;		
			}
			return $return;
		}
		$content = '';
		$content_date ='';
		$content_time ='';
		$i = 0;

		$N = date('N');
		$ii = 0;
		$date = time();
		for ($j = 0; $j < 15; $j++){
			$times = '';
			$date = date("d-m-Y", time() + $j * 24 * 60 * 60);
			$content_date .= '<div class="tab-head day"><div><span class="label-h1">'.day_of_week($N + $j).'</span><span class="label-h2">'.date("j", strtotime($date)).' '.month(date("n", strtotime($date))).'</span></div></div>';
			for ($i = 0; $i < 24; $i++){
				$t = str_pad($i, 2, '0', STR_PAD_LEFT).':00';
				$time = date("$t d.m.Y", strtotime($date));
				$stime = strtotime($time);
				if ($stime < time() + 60 * 60)$status = ' deny';
				else $status = ' active';
				$times .= '<span class="time-item'.$status.'" data-time="'.$time.'" data-time2="'.strtotime($time).'">'.$t.'</span>';
			}
			$content_time .= '<section>'.$times.'</section>';
		}
		$content = '<div class="tabs">'.$content_date.'</div><div class="box-content">'.$content_time.'</div>';
		return $content;
	}

}
