<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;
use Response;


class Comment extends Model {
	
	
	public function user()
    {
        return $this->belongsTo('App\User');
    }
	
	
	/*
	* Вывод комментариев
	*/
	public function showComment($gallery_id = ''){
		$comment = $this
			->where('gallery_id', '=',$gallery_id)
			->with('user')
			->orderBy('created_at')
			->get();
			

		return $comment;
	}
	
	
	/*
	* Добавление комментария
	*/
	public function addComment($gallery_id = '', $comment = ''){
		$res = array();
		if(Auth::check() AND $gallery_id != ''  AND $comment != ''){
			
			$newComment = new Comment;
			$newComment->gallery_id = $gallery_id;
			$newComment->user_id = Auth::user()->id;
			$newComment->comment = $comment;
			$newComment->save();
					
			$res = array(
				"status" => 'success',
				"message" => 'Ok'
			);
			
		}else{
			if($gallery_id == ''){
				$res = array(
					"status" => 'error',
					"message" => 'Нет идентификатора галереи'
				);
			}elseif($comment == ''){
				$res = array(
					"status" => 'error',
					"message" => 'Введите текст комментария'
				);
			}else{
				$res = array(
					"status" => 'error',
					"message" => 'Необходимо авторизоваться'
				);
			}
		}
		
		return Response::json($res);
	}

}
