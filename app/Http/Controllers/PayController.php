<?php namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Redirect;
use Auth;
use Mail;

use App\Pay;
use App\Gallery;
use App\Status;
use App\Setting;
use App\User;
use App\Monitor;
use App\Tarif;
use App\LikeAdmin;
use Carbon\Carbon;
class PayController extends Controller {
	
	public function conditions($gallery_id)
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
	
		$gallery = Gallery::where('id', '=', $gallery_id)->first();
		$monitor = '';
		$tarif = '';
		$date_show = '';
		if(count($gallery) > 0){
			$monitor = Monitor::find($gallery->monitor_id);
			$tarif = Tarif::find($gallery->tarif_id);
			$date_show = $gallery->date_show;
			//$monitor = $monitor->number;
			//$tarif = $tarif->name. ' '.$tarif->desc_main.' '.$tarif->desc_dop;
		}
		if($gallery->user_id != Auth::user()->id){
			Session::flash('message', 'Вы не можете оплатить так как заказ не ваш');
			 return redirect()->route('main');
		}
		
		//Выход из системы для анонимных пользователей
		if(Auth::user()->email == 'anonymous@anonymous.ru'){
			Auth::logout();
		}
		
		return view('pages.pay.conditions')
			->with('monitor', $monitor)
			->with('tarif', $tarif)
			->with('date_show', date('j.m.Y H:i', strtotime($date_show)))
			->with('gallery_id', $gallery_id);
	}
	
	
	public function index($gallery_id)
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
	
		$gallery = Gallery::where('id', '=', $gallery_id)->first();
		$pay = Pay::where('gallery_id', '=', $gallery_id)->first();
		
		if($gallery->user_id != Auth::user()->id){
			Session::flash('message', 'Вы не можете оплатить так как заказ не ваш');
			 return redirect()->route('main');
		}
		
		$setting = new Setting;
		
		$mrh_login = $setting->getPaymentLogin();
		$mrh_pass1 = $setting->getPaymentPassword1();
		$inv_id = $pay->id;
		$inv_desc = $pay->name;
		$out_summ = $pay->price;
		$in_curr = "";
		$culture = "ru";
		$Shp_user = $gallery->user_id;
		
		$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_user=$Shp_user");
		
		$url = 'https://merchant.roboxchange.com/Index.aspx';
		
		if($setting->getPaymentTest() == 1){
			$url = 'http://test.robokassa.ru/Index.aspx';
		}
		
	
		$query = http_build_query(array(
			'MrchLogin' => $mrh_login,
			'OutSum' => $out_summ,
			'InvId' => $inv_id,
			'Desc' => $inv_desc,
			'SignatureValue' => $crc,
			'IncCurrLabel' => $in_curr,
			'Culture' => $culture,
			'Shp_user' => $Shp_user,
		));
		
		
		//Выход из системы для анонимных пользователей
		if(Auth::user()->email == 'anonymous@anonymous.ru'){
			Auth::logout();
		}
		return Redirect::to($url.'?'.$query);
	}
	
	public function result()
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
	
	
		$setting = new Setting;
				
		$mrh_pass2 = $setting->getPaymentPassword2();
		$tm=getdate(time()+9*3600);
		$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";
		
		$out_summ = $_REQUEST["OutSum"];
		$inv_id = $_REQUEST["InvId"];
		$Shp_user = $_REQUEST["Shp_user"];
		$crc = $_REQUEST["SignatureValue"];
		$crc = strtoupper($crc);
		
		$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_user=$Shp_user"));
		
		if ($my_crc !=$crc){
			return "bad sign\n";
			exit();
		}
		
		/* Смена статуса на оплачен */
		$status_pay = Status::where('type_status', '=', 'pay')->where('caption', '=', 'paid')->first();
		$pay = Pay::find($inv_id);
		$pay->status_pay = $status_pay->id;
		$pay->save();
		
		/* При выставление статуса оплачено устанавливаем значение начала модерации */
		$gallery = Gallery::find($pay->gallery_id);
		if(count($gallery) > 0){
			$gallery->start_moderation = Carbon::now();
			$gallery->save();
		}
		
		/* По умолчанию накрутка лайков = 0*/
		$likeAdmin = LikeAdmin::where('gallery_id', '=', $pay->gallery_id)->first();
		if(count($likeAdmin) == 0){
			$likeAdmin = new LikeAdmin;
			$likeAdmin->gallery_id = $pay->gallery_id;
			$likeAdmin->count = 0;
			$likeAdmin->save();
		}
		
		
		/*
		* Отправка уведомления после оплаты
		*/
		$settingModel = new Setting;
		$result = $settingModel->getGallerySendEmails();
		$user = User::find($gallery->user_id);
		if($result->value != ''){				
			$key = Array(
				"name" => iconv("UTF-8", "cp1251", $user->name),
				"email" => $user->email,
				"gallery_id" => $gallery->id,
				"date_show" => $gallery->date_show,
			);
			$emails = explode(',', $result->value);
			Mail::send('mail.newGallery', ['key' => $key], function($message) use ($emails) 
			{
				foreach($emails as $email){
					$message->to(trim($email))->subject('Уведомление о новом заказе');
				}
			});
		}
		
		
		
		
		$f=@fopen(base_path()."/public/pay/order.txt","a+") or
				  die("error");
		fputs($f,"order_num :$inv_id;Summ :$out_summ;Date :$date;User :$Shp_user\n");
		fclose($f);
		
		//Выход из системы для анонимных пользователей
		if(Auth::user()->email == 'anonymous@anonymous.ru'){
			Auth::logout();
		}
		
		
		return "OK$inv_id\n";
	
	}
	
	
	public function success(Gallery $galleryModel)
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
		
		$head_name = '';
		if(isset($_REQUEST["OutSum"])){
			$setting = new Setting;
			
			$mrh_pass1 = $setting->getPaymentPassword1();
			$out_summ = $_REQUEST["OutSum"];
			$inv_id = $_REQUEST["InvId"];
			$Shp_user = $_REQUEST["Shp_user"];
			$crc = $_REQUEST["SignatureValue"];
			$crc = strtoupper($crc);
			$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass1:Shp_user=$Shp_user"));
			
			if ($my_crc != $crc){
				return "bad sign\n";
				exit();
			}
			
			$head_name = 'Проведение платежа';
		}
		if(isset($_REQUEST["InvId"])){
			$pay = Pay::find($_REQUEST["InvId"]);
			if(count($pay) > 0){
				$gallery = Gallery::find($pay->gallery_id);
			}
		}
		//dd($gallery);
		
		
		
				
		//Выход из системы для анонимных пользователей
		if(Auth::user()->email == 'anonymous@anonymous.ru'){
			Auth::logout();
		}
		
		return view('pages.pay.success')
			->with('gallery', $gallery)
			->with('pathImages', $galleryModel->pathImages)
			->with('head_name', $head_name);
	}
	
	
	
	public function fail()
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
	
		$Shp_user = $_REQUEST["Shp_user"];
		$inv_id = $_REQUEST["InvId"];
		
		if($Shp_user != Auth::user()->id){
			Session::flash('message', 'Доступ запрещен');
			 return redirect()->route('main');
		}
		
		/* Смена статуса на Отклонено пользователем */
		$status_pay = Status::where('type_status', '=', 'pay')->where('caption', '=', 'cancelUser')->first();
		$pay = Pay::find($inv_id);
		$pay->status_pay = $status_pay->id;
		$pay->save();
		
		
		//Выход из системы для анонимных пользователей
		if(Auth::user()->email == 'anonymous@anonymous.ru'){
			Auth::logout();
		}
		
		$result = "Вы отказались от оплаты. Заказ# ".$inv_id;
		return view('pages.pay.fail')->with('result', $result);
	}
	
}