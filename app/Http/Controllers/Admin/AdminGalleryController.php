<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Session;
use File;

use App\Gallery;
use App\Status;
use App\Pay;
use App\Like;
use App\Comment;


class AdminGalleryController extends Controller {

	/*
	* Вывод заказов в админке
	*/
	public function index(Gallery $galleryModel)
	{
		
		$data = array(
			"galleryModeration" => $galleryModel->getGalleryModeration(),
			"gallerySuccess" => $galleryModel->getGallerySuccess(),
			"galleryCancel" => $galleryModel->getGalleryCancel(),
			"pathImages" => $galleryModel->pathImages,
		);
		return view('admin.gallery.index')->with('data', $data);
	}

	/*
	* Выставление статуса одобрена для одной записи
	*/
	public function success($id)
	{
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'success')->first();
		$this->changeStatus($id, $status_main->id);
		Session::flash('message', 'Заказ одобрен');
		return redirect()->route('admin.gallery.index');
	}
	
	/*
	* Выставление статуса отклонено для одной записи
	*/
	public function cancel($id)
	{
		$status_main = Status::where('type_status', '=', 'main')->where('caption', '=', 'cancel')->first();
		$this->changeStatus($id, $status_main->id);
		Session::flash('message', 'Заказ отклонен');
		return redirect()->route('admin.gallery.index');
	}
	
	/*
	* Изменение статуса для одной записи
	*/
	public function changeStatus($id, $status_id)
	{
        $gallery = Gallery::find($id);
		$gallery->status_main = $status_id;
		$gallery->save();
		return $gallery;
	}
	


	/*
	* Удаление заказа
	*/
	public function delete(Gallery $modelGallery, $id)
	{
		$gallery = Gallery::find($id);
		
		/* В Pay отвязываемся от галереи */
		$pay = Pay::where('gallery_id', '=', $id)->first();
		if(count($pay) > 0){
			$pay->gallery_id = null;
			$pay->save();
		}
		
		/* Удаление лайков */
		$like = Like::where('gallery_id', '=', $id);
		if(count($like) > 0){$like->delete();}
		
		/* Удаление Комментариев */
		$comment = Comment::where('gallery_id', '=', $id);
		if(count($comment) > 0){$comment->delete();}

		/* Удаление файлов */
		$file1 = base_path().$modelGallery->pathImages.'/s_'.$gallery->src;
		$file2 = base_path().$modelGallery->pathImages.'/m_'.$gallery->src;
		$file3 = base_path().$modelGallery->pathImages.'/o_'.$gallery->src;
		File::delete($file1, $file2, $file3);
		
		/* Удаление самой галереи */
		$gallery->delete();
		 
		Session::flash('message', 'Заказ удален');
		return redirect()->route('admin.gallery.index');
	}

}
