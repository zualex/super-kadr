<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Session;
use Redirect;
use Auth;
use App\Pay;
use App\Gallery;
use App\Status;

class PayController extends Controller {

	
	public function conditions($gallery_id)
	{
	
		$gallery = Gallery::where('id', '=', $gallery_id)->first();
		if($gallery->user_id != Auth::user()->id){
			Session::flash('message', 'Вы не можете оплатить так как заказ не ваш');
			 return redirect()->route('main');
		}
		
		return view('pages.pay.conditions')->with('gallery_id', $gallery_id);
	}
	
	
	public function index($gallery_id)
	{

		$gallery = Gallery::where('id', '=', $gallery_id)->first();
		$pay = Pay::where('gallery_id', '=', $gallery_id)->first();
		
		if($gallery->user_id != Auth::user()->id){
			Session::flash('message', 'Вы не можете оплатить так как заказ не ваш');
			 return redirect()->route('main');
		}
	
		$mrh_login = env('ROBOKASSA_LOGIN');
		$mrh_pass1 = env('ROBOKASSA_PASSWORD_1');

		$inv_id = $pay->id;
		$inv_desc = $pay->name;
		$out_summ = $pay->price;
		$in_curr = "";
		$culture = "ru";
		$Shp_user = $gallery->user_id;
		
		$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_user=$Shp_user");
		
		$url = 'https://merchant.roboxchange.com/Index.aspx';
		if(env('ROBOKASSA_TEST')){
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

		return Redirect::to($url.'?'.$query);
	}

	
	public function result()
	{
		$mrh_pass2 = env('ROBOKASSA_PASSWORD_2');
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
		
		
		
		$f=@fopen(base_path()."/public/pay/order.txt","a+") or
				  die("error");
		fputs($f,"order_num :$inv_id;Summ :$out_summ;Date :$date;User :$Shp_user\n");
		fclose($f);

		
		return "OK$inv_id\n";
	
	}
	
	
	public function success()
	{
		$mrh_pass1 = env('ROBOKASSA_PASSWORD_1');
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
		
		$result = "Произошла ошибка";
		
		$f=@fopen(base_path()."/public/pay/order.txt","a+") or die("error");
		while(!feof($f)){
			$str=fgets($f);

			$str_exp = explode(";", $str);
			if ($str_exp[0]=="order_num :$inv_id"){ 
				$result = "Операция прошла успешно";
			}
		}
		fclose($f);
		
		return view('pages.pay.success')->with('result', $result);
	}
	
	
	
	public function fail()
	{
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
		
		$result = "Вы отказались от оплаты. Заказ# ".$inv_id;
		return view('pages.pay.fail')->with('result', $result);
	}
	
}