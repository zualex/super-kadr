<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use Response;
use Request;
use Session;
use Input;
use Auth;

use App\Monitor;
use App\Gallery;
use App\Pay;
use App\Like;
use App\Comment;
use App\User;
use App\Setting;


class GalleryController extends Controller {

	
	public function index(Gallery $galleryModel)
	{	
		$data = array(
			'gallery' => $galleryModel->galleryAll(),
			'pathImages' => $galleryModel->pathImages,
		);
		return view('pages.gallery.index')->with('data', $data);
	}
	
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Gallery $galleryModel, Comment $commentModel, User $userModel,  $id)
	{	
		
		return view('pages.gallery.show')
			->with('gallery', $galleryModel->getGallery($id))
			->with('comments', $commentModel->showComment($id))
			->with('defaultAvatar', $userModel->defaultAvatar);
	}
	
	
		
	public function upload(Gallery $galleryModel)
	{
		//Если не авторизован то авторизуемся как анонимы
		if(!Auth::check()){
			$user = User::where('email', '=', "anonymous@anonymous.ru")->first();
			if(!$user){
				$user = new User;
				$user->name = "Анонимный пользователь";
				$user->email = "anonymous@anonymous.ru";
				$user->save();
			}
			Auth::loginUsingId($user->id);
		}
	
		if(Auth::check()){
			$imgUrl = $_POST['imgUrl'];
			$imgInitW = $_POST['imgInitW'];			// original sizes
			$imgInitH = $_POST['imgInitH'];
			$imgW = $_POST['imgW'];					// resized sizes
			$imgH = $_POST['imgH'];
			$imgY1 = $_POST['imgY1'];					// offsets
			$imgX1 = $_POST['imgX1'];
			$cropW = $_POST['cropW'];					// crop box
			$cropH = $_POST['cropH'];
			$angle = $_POST['rotation'];					// rotation angle
			$jpeg_quality = 100;
			
			$monitor = $_POST['monitor'];
			$modelMonitor = Monitor::find($monitor);
			if(count($modelMonitor) > 0){
				$origW = $modelMonitor->origWidth;
				$origH = $modelMonitor->origHeight;
				$ratioW = $origW/$cropW;
				$ratioH = $origH/$cropH;

				$imgW = $imgW*$ratioW;
				$imgH = $imgH*$ratioH;
				$imgY1 = $imgY1*$ratioW;
				$imgX1 = $imgX1*$ratioH;
				$cropW = $cropW*$ratioW;
				$cropH = $cropH*$ratioH;
			}
			
			if($imgW <= 10000 and $imgH <= 10000 and $imgInitW <= 10000 and $imgInitH <= 10000){
				
				$dirFile = $galleryModel->pathImages . "/temp/".Auth::user()->id;
				if (!file_exists(base_path().$dirFile)) {mkdir(base_path().$dirFile, 0755, true);}		//Создание папки если нет
				array_map('unlink', glob(base_path().$dirFile."/*"));																//Удаление всех файлов для определенного пользователя
				
				$output_filename = $dirFile."/croppedImg_".rand();
				
				$what = getimagesize($imgUrl);
				switch(strtolower($what['mime']))
				{
					case 'image/png':
						$img_r = imagecreatefrompng($imgUrl);
						$source_image = imagecreatefrompng($imgUrl);
						$type = '.png';
						break;
					case 'image/jpeg':
						$img_r = imagecreatefromjpeg($imgUrl);
						$source_image = imagecreatefromjpeg($imgUrl);
						error_log("jpg");
						$type = '.jpg';
						break;
					case 'image/gif':
						$img_r = imagecreatefromgif($imgUrl);
						$source_image = imagecreatefromgif($imgUrl);
						$type = '.gif';
						break;
					default: die('image type not supported');
				}
				if(is_writable(dirname(base_path().$output_filename))){

					// resize the original image to size of editor
					$resizedImage = imagecreatetruecolor($imgW, $imgH);
					imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);
					// rotate the rezized image
					$rotated_image = imagerotate($resizedImage, -$angle, 0);
					// find new width & height of rotated image
					$rotated_width = imagesx($rotated_image);
					$rotated_height = imagesy($rotated_image);
					// diff between rotated & original sizes
					$dx = $rotated_width - $imgW;
					$dy = $rotated_height - $imgH;
					// crop rotated image to fit into original rezized rectangle
					
					//ini_set('memory_limit', '1024M'); 
					$cropped_rotated_image = imagecreatetruecolor($imgW, $imgH);
					imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
					imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH);
					// crop image into selected area
					$final_image = imagecreatetruecolor($cropW, $cropH);
					imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
					imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);
					// finally output png image
					//imagepng($final_image, base_path().$output_filename.$type, $png_quality);
					imagejpeg($final_image, base_path().$output_filename.$type, $jpeg_quality);
					
					
					
					
					//Выход из системы для анонимных пользователей
					if(Auth::user()->email == 'anonymous@anonymous.ru'){
						Auth::logout();
					}

					
					
					return Response::json( array(
						"status" => 'success',
						"url" => $output_filename.$type
					));


				
				
				}else{
					return Response::json( array(
						"status" => 'error',
						"message" => 'Файл не может быть сохранен'
					));
				}			
			}else{
				return Response::json( array(
					"status" => 'error',
					"message" => 'Размер изображения больше 10000x10000'
				));
			}
		}else{
			return Response::json( array(
				"status" => 'error',
				"message" => 'Необходимо авторизоваться'
			));
		}
	}

	
	public function create(Gallery $galleryModel, Pay $payModel)
	{
	
		//Если не авторизован то авторизуемся как анонимы
		if(!Auth::check()){
			$user = User::where('email', '=', "anonymous@anonymous.ru")->first();
			if(!$user){
				$user = new User;
				$user->name = "Анонимный пользователь";
				$user->email = "anonymous@anonymous.ru";
				$user->save();
			}
			Auth::loginUsingId($user->id);
		}
	
	
		$error = "";
		$paramGallery = array(
			'monitor' => Request::input('monitor'),
			'image' => Request::input('image'),
			'tarif' => Request::input('tarif'),
			'dateShow' => Request::input('dateShow'),
		);		
		//Создание галереи
		$gallery = $galleryModel->createGallery($paramGallery);
		if($gallery){
			//Создание заказа
			$param = array(
				'gallery_id' => $gallery->id,
				'tarif' => Request::input('tarif'),
			);	
			$pay = $payModel->createPay($param);
		}
		
		if(count($galleryModel->error) > 0){$error .= implode(', ', $galleryModel->error);}
		if(count($payModel->error) > 0){$error .= implode(', ', $payModel->error);}
		if($error == ''){
		
			$pay = "";
			$url = "";
			
			
			$setting = new Setting;
			$robokassa = $setting->getPayment();
			if($setting->getPayment() == 1){
				$pay = "true";
				$url = route('pay.conditions', $gallery->id);
			}
			
			//Выход из системы для анонимных пользователей
			if(Auth::user()->email == 'anonymous@anonymous.ru'){
				Auth::logout();
			}

					
			return Response::json( array(
				"status" => 'success',
				"message" => 'Заказ отправлен',
				"pay" => $pay,
				"url" => $url,
			));
		}else{
			//Выход из системы для анонимных пользователей
			if(Auth::user()->email == 'anonymous@anonymous.ru'){
				Auth::logout();
			}

		
			return Response::json( array(
				"status" => 'error',
				"message" => $error,
			));
		}

		
	}
	
	
	/*
	* Лайк
	*/
	public function like(Like $likeModel)
	{
		$like = $likeModel->likeClick(Request::input('gallery'));
		return $like;		
	}
	
	
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	
}
