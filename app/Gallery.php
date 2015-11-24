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
	
	public function like_admins()
    {
        return $this->hasOne('App\LikeAdmin');
    }
	
	public function comments()
    {
        return $this->hasMany('App\Comment');
    }
	
	public function user()
    {
        return $this->belongsTo('App\User');
    }
	
	
	
	/*
	* galleryAll - вывод всех галерей
	*/
	public function galleryAll(){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		
		//(COUNT(likes.id)+SUM(like_admins.count)) AS like_count
		//COUNT(likes.id) AS like_count
		$galleries =$this
				->select(DB::raw('galleries.*, (COUNT(likes.id)+SUM(like_admins.count)) AS like_count,  (SELECT COUNT(comments.id) FROM comments WHERE comments.gallery_id = galleries.id) as comment_count'))
				->leftJoin('likes', 'galleries.id', '=', 'likes.gallery_id')
				->leftJoin('like_admins', 'galleries.id', '=', 'like_admins.gallery_id')
				->where('status_main', '=', $status_main->id)
				->groupBy('galleries.id')
				->orderBy('date_show', 'desc')
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
					((SELECT COUNT(likes.id) FROM likes WHERE likes.gallery_id = g.id)+(SELECT like_admins.count FROM like_admins WHERE like_admins.gallery_id = g.id)) as like_count,  
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
					SELECT g.*,  (COUNT(l.id)+SUM(l_a.count)) AS like_count,  (SELECT COUNT(comments.id) FROM comments WHERE comments.gallery_id = g.id) as comment_count
					FROM galleries as g
					LEFT JOIN likes as l ON l.gallery_id = g.id
					LEFT JOIN like_admins as l_a ON l_a.gallery_id = g.id
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
				->with('like_admins')
				->with('comments')
				->first();
			$gallery->pathImages = $this->pathImages;
		}
		//dd($gallery);
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
			$tarif = Tarif::find($param['tarif']);
			$count_show = 0;
			if($tarif){
				$count_show = $tarif->hours*60*60 / $tarif->interval_sec;
			}
		
			$gallery = new Gallery;
			$gallery->user_id = Auth::user()->id;
			$gallery->status_main = $status_main->id;
			$gallery->status_order = $status_order->id;
			$gallery->date_show = Carbon::createFromFormat('H:i d.m.Y', $param['dateShow']);
			$gallery->count_show = $count_show;
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
	public function getGalleryModeration($way = 'asc', $dateFrom = '', $dateTo = ''){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'moderation')->first();
		$gallery =$this->queryAdminGallery($status_main->id, $way, $dateFrom, $dateTo);

		return $gallery;
	}
	
	/*
	* Список галереии со статусом на одобрено
	*/
	public function getGallerySuccess($way = 'desc', $dateFrom = '', $dateTo = ''){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		$gallery =$this->queryAdminGallery($status_main->id, $way, $dateFrom, $dateTo);

		return $gallery;
	}	
	
	/*
	* Список галереии со статусом на отменено
	*/
	public function getGalleryCancel($way = 'desc', $dateFrom = '', $dateTo = ''){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'cancel')->first();
		$gallery =$this->queryAdminGallery($status_main->id, $way, $dateFrom, $dateTo);

		return $gallery;
	}
	
	public function queryAdminGallery($status, $way = 'desc', $dateFrom = '', $dateTo = ''){
		$status_pay = Status::where('type_status', '=', 'pay')->where('caption', '=', 'paid')->first();
		
		$nowDate = Carbon::now();
		if($dateFrom == ''){$dateFrom = $nowDate->format('Y-m-d');}
		if($dateTo == ''){$dateTo = $nowDate->format('Y-m-d');}
		$dateTo = Carbon::parse($dateTo)->addDay(1);
		
		$gallery =$this
				->select(DB::raw('galleries.*, COUNT(likes.id) as like_count, like_admins.count AS like_admins_count, pays.id as pay_id, pays.price, tarifs.name as tarif_name, tarifs.hours, tarifs.interval_sec, statuses.name as status_name, statuses.caption as status_caption, users.name as user_name, users.provider'))
				->join('statuses', 'statuses.id', '=', 'galleries.status_order')
				->leftJoin('likes', 'galleries.id', '=', 'likes.gallery_id')
				->leftJoin('like_admins', 'galleries.id', '=', 'like_admins.gallery_id')
				->leftJoin('pays', 'pays.gallery_id', '=', 'galleries.id')
				->leftJoin('users', 'users.id', '=', 'galleries.user_id')
				->join('tarifs', 'tarifs.id', '=', 'galleries.tarif_id')
				->where('galleries.status_main', '=', $status)
				->where('pays.status_pay', '=', $status_pay->id)
				->where('galleries.date_show', '>=', $dateFrom)
				->where('galleries.date_show', '<=', $dateTo)
				->groupBy('galleries.id')
				->orderBy('galleries.date_show', $way)
				->get();
		return $gallery;
	}
	
	
	/*
	* Получение галереи для конкурса
	*
	*	user_id дуюдтруется так как при сортировку массива ключ обнуляется
	*
	*	$res = array(
	*		'user_id' => array(									//id пользователя
	*			'user_id' => $user_id,							//id пользователя
	*			'name' => $value->user->name,			//Имя пользователя
	*			'provider' => $value->user->provider,	//Социальная сеть
	*			'avatar' => $value->user->avatar,			//аватарка
	*			'item' => array(),									//галерея
	*			'all_like' => 0,										//Кол-во всех лайков
	*			'max_like' => 0,									//Мак кол-во лайков за одну фото
	*			'count' => 0,											//Кол-во фото
	*		)
	*	)
	*/
	public function getGalleryCompetition(){
		$res = array();
		$gallery = array();
				
		$competition = Competition::first();
		if(count($competition) > 0 ){
			$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
			$start_select = '0000-00-00 00:00:00';
			$end_select = '9999-12-30 23:59:59';
			if($competition->start_select){$start_select = $competition->start_select;}
			if($competition->end_select){$end_select = Carbon::parse($competition->end_select)->addDay(1);}
			
			$gallery =$this
				->with('user')
				->with('likes')
				->with('like_admins')
				->with('comments')
				->where('status_main', '=', $status_main->id)
				->where('date_show', '>=', $start_select)
				->where('date_show', '<=', $end_select)
				->get();
			
			if(count($gallery) > 0){
				$count = 0;
				foreach($gallery as $key => $value){
					$count++;
					$user_id = $value->user_id;
					$email = $value->user->email;
					if($email != 'anonymous@anonymous.ru'){
					
						if(!array_key_exists($user_id, $res)){
							$res[$user_id] = array(
								'user_id' => $user_id,
								'name' => $value->user->name,
								'provider' => $value->user->provider,
								'avatar' => $value->user->avatar,
								'item' => array(),
								'all_like' => 0,
								'max_like' => 0,
								'count' => 0,
							);
						}
						
						$like = 0;
						$like_admin = 0;
						if($value->likes){$like = count($value->likes);}
						if($value->like_admins){$like_admin = $value->like_admins->count;}
						$like = $like+$like_admin;
						
						$value->like_count = $like;
						$value->comment_count = count($value->comments);
						
						$res[$user_id]['item'][] = $value;
						$res[$user_id]['all_like'] += $like;
						if($res[$user_id]['max_like'] < $like){
							$res[$user_id]['max_like'] = $like;
						}
						$res[$user_id]['count'] += 1;
					}
				}
				
				//Сортировка item по кол-ву лайков
				foreach($res as $key => $value){
					$res[$key]['item'] = $this->array_orderby($value['item'], 'like_count', SORT_DESC);
				}
				
				//Макс кол-во лайков по всем фото клиента
				if($competition->condition == 'like_all_foto'){
					$res = $this->array_orderby($res, 'all_like', SORT_DESC, 'count', SORT_DESC);
				}
				
				//Макс кол-во лайков на одну фото клиента
				if($competition->condition == 'like_one_foto'){
					$res = $this->array_orderby($res, 'max_like', SORT_DESC, 'all_like', SORT_DESC);
				}
				
				//Макс кол-во фото у одного клиента
				if($competition->condition == 'foto_all_user'){
					$res = $this->array_orderby($res, 'count', SORT_DESC, 'all_like', SORT_DESC);
				}
				
				
			}
			
		}
		

		return $res;
	}
	
	

	/*
	*	Получение ТОП 10 фото
	*/
	public function getTop($arrRes, $limit){
		$res = array();
		
		$count = 0;
		foreach($arrRes as $key => $value){
			foreach($value['item'] as $item){
				$count++;
				if($count <= $limit){
					$res[] = $item;
				}
			}
		}
		$res = $this->array_orderby($res, 'like_count', SORT_DESC);

		return $res;
	}
	
	
	
	/*
	*	Получение ТОП авторов
	*/
	public function getAutor($arrRes){
		$res = array();
		
		$count = 0;
		foreach($arrRes as $key => $value){
			$count++;
			$res[] = array(
				'user_id' => $value['user_id'],
				'name' => $value['name'],
				'provider' => $value['provider'],
				'avatar' => $value['avatar'],
				'count' => $value['count'],
			);
		}

		return $res;
	}	
	
	
	
	/*
	*	Получение галереи пользователя
	*/
	public function getAutorGallery($arrRes, $user_id){
		$res = array();
		
		foreach($arrRes as $key => $value){
			if($user_id == $value['user_id']){
				foreach($value['item'] as $item){
					$res[] = $item;
				}
			}
		}
		$res = $this->array_orderby($res, 'like_count', SORT_DESC);
		return $res;
	}
	
	
	
	/*
	* dopGalleryCompetitionAll - вывод всех галерей для конкурсов
	*/
	public function getGalleryCompetitionAll(){
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		
		$competition = Competition::first();
		if(count($competition) > 0 ){
			$start_select = '0000-00-00 00:00:00';
			$end_select = '9999-12-30 23:59:59';
			if($competition->start_select){$start_select = $competition->start_select;}
			if($competition->end_select){$end_select = Carbon::parse($competition->end_select)->addDay(1);}
			
				
			$galleries =$this
				->select(DB::raw('galleries.*, (COUNT(likes.id)+SUM(like_admins.count)) AS like_count,  (SELECT COUNT(comments.id) FROM comments WHERE comments.gallery_id = galleries.id) as comment_count'))
				->leftJoin('likes', 'galleries.id', '=', 'likes.gallery_id')
				->leftJoin('like_admins', 'galleries.id', '=', 'like_admins.gallery_id')
				->where('date_show', '>=', $start_select)
				->where('date_show', '<=', $end_select)
				->where('status_main', '=', $status_main->id)
				->groupBy('galleries.id')
				->orderBy('date_show', 'desc')
				->orderBy('like_count', 'desc')
				->orderBy('comment_count', 'desc')
				->paginate($this->limitMain);
		}

		return $galleries;
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

