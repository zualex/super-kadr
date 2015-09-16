<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Hash;
use Session;
use Auth;
use Image;


use Illuminate\Pagination\Paginator;
use Cache;
use DB;
use Carbon\Carbon;

use App\Status;
use App\Monitor;
use App\Tarif;
use App\Like;
use App\Pay;

class Gallery extends Model {
	
	public $error;
	public $pathImages;
	public $limitMain;
	
	public function __construct(){
		$this->error = array();
		$this->pathImages = "/public/images";
		$this->limitMain = 15; //Кол-во галереи на главной
	}

	
	public function pay()
    {
        return $this->hasOne('App\Pay');
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
	* galleryAll - вывод всех галерей
	*/
	public function galleryAll(){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		
		$galleries =$this
				->select(DB::raw('galleries.*, COUNT(likes.id) AS like_count,  (SELECT COUNT(comments.id) FROM comments WHERE comments.gallery_id = galleries.id) as comment_count'))
				->leftJoin('likes', 'galleries.id', '=', 'likes.gallery_id')
				->where('status_main', '=', $status_main->id)
				->groupBy('galleries.id')
				->orderBy('like_count', 'desc')
				->orderBy('comment_count', 'desc')
				->paginate($this->limitMain);
		

		return $galleries;
	}
	
	
	/*
	* mainGallery - вывод на главной
	*/
	public function mainGallery(){
		$gallery = array();

		/* Кэшируем на 30 минут */
		//$expiresAt = Carbon::now()->addMinutes(30);
		//$gallery = Cache::remember('galleryHome', $expiresAt, function()
		//{
			$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
			/* Находим 15 самых популярных за месяц */
			$arrIdGallery = array(0);
			$nowDate = Carbon::now();
			$dateEnd = $nowDate->toDateString();
			$dateStart = $nowDate->subMonth()->toDateString();
			
			/*
			* like_count считается отдельно так как есть ограничения что лайки считаются за месяц а нужно за весь период
			*/
			$galleryTop = DB::select('
				SELECT 
					g.*,  
					(SELECT COUNT(likes.id) FROM likes WHERE likes.gallery_id = g.id) as like_count,  
					(SELECT COUNT(comments.id) FROM comments WHERE comments.gallery_id = g.id) as comment_count
				FROM galleries as g
				LEFT JOIN likes as l ON l.gallery_id = g.id
				WHERE 
					status_main = ?
					AND date(l.created_at) BETWEEN "'.$dateStart.'" AND "'.$dateEnd.'"
				GROUP BY g.id
				ORDER BY like_count DESC, comment_count DESC
				LIMIT ?', [$status_main->id, $this->limitMain]
			);
			/* 
			*	Сохраняем список id и узнаем реальное кол-во  лайков так как запрос был
			*/
			foreach($galleryTop as $key => $value){
				$arrIdGallery[$value->id] = $value->id;
			}
			
			
			/* Если 15 нет то добираем из всех */
			if(count($galleryTop) < $this->limitMain){
				$limit = $this->limitMain - count($galleryTop);
				$galleryDop = DB::select('
					SELECT g.*,  COUNT(l.id) AS like_count,  (SELECT COUNT(comments.id) FROM comments WHERE comments.gallery_id = g.id) as comment_count
					FROM galleries as g
					LEFT JOIN likes as l ON l.gallery_id = g.id
					WHERE 
						status_main = ?
						AND g.id NOT IN ('.implode(",", $arrIdGallery).')
					GROUP BY g.id
					ORDER BY like_count DESC, comment_count DESC
					LIMIT ?', [$status_main->id, $limit]
				);
				foreach($galleryDop as $key => $value){
					$arrIdGallery[$value->id] = $value->id;
					$galleryTop[] = $value;
				}
			}
			$gallery['galleries'] = $galleryTop;
			$gallery['pathImages'] = $this->pathImages;
		//	return $gallery;
		//});

		
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
	*	'tarif'
	*	'dateShow'
	*/
	public function createGallery($param){
		$gallery = false;
		if(!array_key_exists('monitor', $param)){$param['monitor'] = '';}
		if(!array_key_exists('image', $param)){$param['image'] = '';}
		if(!array_key_exists('tarif', $param)){$param['tarif'] = '';}
		if(!array_key_exists('dateShow', $param)){$param['dateShow'] = '';}
		
		if($param['monitor'] == ''){$this->error[] = 'Не выбран экран';}
		if($param['image'] == ''){$this->error[] = 'Не загружено фото';}
		if($param['tarif'] == ''){$this->error[] = 'Не выбран тариф';}
		if($param['dateShow'] == ''){$this->error[] = 'Не выбрана дата и начало паказа';}
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
			$status_order = Status::where('type_status', '=', 'order')->where('caption', '=', 'queue')->first();
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
			$gallery->date_show = Carbon::createFromFormat('H:i d.m.Y', $param['dateShow']);
			$gallery->tarif_id = $param['tarif'];
			$gallery->monitor_id = $param['monitor'];
			$gallery->save();
			
			$galleryId = $gallery->id;
			$src = $galleryId.$ext;
			
			//Update Gallery (update src)
			$gallery->src = $src;
			$gallery->save();
			
			
			
			Image::make(base_path().$dir.'/'.$uploadImage)->save(base_path().$this->pathImages.'/o_'.$src);
			Image::make(base_path().$dir.'/'.$uploadImage)->resize($sizeImg['mediumWidth'], $sizeImg['mediumHeight'])->save(base_path().$this->pathImages.'/m_'.$src);
			Image::make(base_path().$dir.'/'.$uploadImage)->resize($sizeImg['smallWidth'], $sizeImg['smallHeight'])->save(base_path().$this->pathImages.'/s_'.$src);
			array_map('unlink', glob(base_path().$dir."/*"));	//Очистка temp
			
		}
		
		return $gallery;
	}
	
	
	/*
	* Список галереии со статусом на модерации
	*/
	public function getGalleryModeration(){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'moderation')->first();
		$gallery =$this->queryAdminGallery($status_main->id);

		return $gallery;
	}
	
	/*
	* Список галереии со статусом на одобрено
	*/
	public function getGallerySuccess(){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		$gallery =$this->queryAdminGallery($status_main->id);

		return $gallery;
	}	
	
	/*
	* Список галереии со статусом на отменено
	*/
	public function getGalleryCancel(){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'cancel')->first();
		$gallery =$this->queryAdminGallery($status_main->id);

		return $gallery;
	}
	
	public function queryAdminGallery($status){
		$status_pay = Status::where('type_status', '=', 'pay')->where('caption', '=', 'paid')->first();

		$gallery =$this
				->select(DB::raw('galleries.*, pays.id as pay_id, pays.price, tarifs.name as tarif_name, tarifs.hours, tarifs.interval_sec, statuses.name as status_name, statuses.caption as status_caption'))
				->join('statuses', 'statuses.id', '=', 'galleries.status_order')
				->leftJoin('pays', 'pays.gallery_id', '=', 'galleries.id')
				->join('tarifs', 'tarifs.id', '=', 'galleries.tarif_id')
				->where('galleries.status_main', '=', $status)
				->where('pays.status_pay', '=', $status_pay->id)
				->orderBy('galleries.date_show', 'asc')
				->get();
		return $gallery;
	}

}
