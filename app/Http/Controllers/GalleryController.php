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


class GalleryController extends Controller {

	
	public function index()
	{
		return view('pages.gallery.index');
	}
	
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Gallery $galleryModel, $id)
	{
		$gallery = $galleryModel->getGallery($id);
		return view('pages.gallery.show')->with('gallery', $gallery);
	}
	
	
		
	public function upload(Gallery $galleryModel)
	{
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
						$type = '.jpeg';
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
		$error = array();
		$paramGallery = array(
			'monitor' => Request::input('monitor'),
			'image' => Request::input('image'),
		);		
		//Создание галереи
		$gallery = $galleryModel->createGallery($paramGallery);
		if($gallery){
			//Создание заказа
			$param = array(
				'gallery_id' => $gallery->id,
				'tarif' => Request::input('tarif'),
				'monitor' => Request::input('monitor'),
				'dateShow' => Request::input('dateShow'),
			);	
			$pay = $payModel->createPay($param);
		}
		
		if(count($galleryModel->error) > 0){$error[] = $galleryModel->error;}
		if(count($payModel->error) > 0){$error[] = $payModel->error;}


		return Response::json( array(
			"error" => $error,
		));
	}
	
	
	
	public function like(Gallery $galleryModel)
	{
		if(Auth::check()){
			$inc = 0;
			$gallery_id = Request::input('gallery');
			
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
			return Response::json( array(
				"status" => 'success',
				"message" => $inc
			));
			
		}else{
			return Response::json( array(
				"status" => 'error',
				"message" => 'Необходимо авторизоваться'
			));
		}
		
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
