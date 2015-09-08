<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;
use Response;

class Like extends Model {

	
	public function likeClick($gallery_id = ''){
		$res = array();
		if(Auth::check() AND $gallery_id != ''){
			
			$inc = 0;
			$like = Like::where('user_id', '=', Auth::user()->id)
				->where('gallery_id', '=', $gallery_id)
				->first();
			
			if(count($like) == 0){
				$inc = 1;
				$like = new Like;
				$like->user_id = Auth::user()->id;
				$like->gallery_id = $gallery_id;
				$like->save();
			}else{
				$inc = -1;
				$like->delete();
			}
			$res = array(
				"status" => 'success',
				"message" => $inc
			);
			
		}else{
			if($gallery_id == ''){
				$res = array(
					"status" => 'error',
					"message" => 'Нет идентификатора галереи'
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
