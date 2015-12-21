<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Comment;
use App\User;
use Request;
use Session;

class CommentController extends Controller {

	/*
	* Вывод комментариев
	*/
	public function index(Comment $commentModel, User $userModel, $gallery_id)
	{
		return view('pages.comment.index')
			->with('comments', $commentModel->showComment($gallery_id))
			->with('defaultAvatar', $userModel->defaultAvatar);
	}
	
	
	/*
	* Сохранение комментария
	*/
	public function save(Comment $commentModel)
	{
		$comment = $commentModel->addComment(Request::input('gallery'), Request::input('text'));
		return $comment;		
	}
	
	
	/*
	* Удаление комментария
	*/
	public function delete($comment_id){
		$comment = Comment::find($comment_id);
		$comment->delete();
		Session::flash('message', 'Комментарий удален');
		return redirect()->back();
	}
	
	
}
