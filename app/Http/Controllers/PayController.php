<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Session;
use Redirect;
use Auth;
use App\Pay;
use App\Gallery;

class PayController extends Controller {

	
	public function conditions($gallery_id)
	{
		$gallery = Gallery::where('id', '=', $gallery_id)->first();
		if($gallery->user_id != Auth::user()->id){
			Session::flash('message', 'Вы не можете оплатить так как заказ не ваш');
			 return redirect()->route('main');
		}
		
		return view('pages.conditions.pay')->with('gallery_id', $gallery_id);
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
		
		$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");
		
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
		$crc = $_REQUEST["SignatureValue"];
		$crc = strtoupper($crc);
		
		$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));
		
		if ($my_crc !=$crc){
			return "bad sign\n";
			exit();
		}
		
		
		$status_pay = Status::where('type_status', '=', 'pay')->where('caption', '=', 'paid')->first();
		$pay = Pay::where('id', '=', $inv_id)->first();
		$pay->status_pay = $status_pay->id;
		$pay->save();
		
		
		
		$f=@fopen(base_path()."/public/pay/order.txt","a+") or
				  die("error");
		fputs($f,"order_num :$inv_id;Summ :$out_summ;Date :$date\n");
		fclose($f);

		
		return "OK$inv_id\n";
	
	}
	
	
	public function success()
	{
		$mrh_pass1 = env('ROBOKASSA_PASSWORD_1');
		$out_summ = $_REQUEST["OutSum"];
		$inv_id = $_REQUEST["InvId"];
		$crc = $_REQUEST["SignatureValue"];
		$crc = strtoupper($crc);

		$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass1"));
		
		if ($my_crc != $crc){
			return "bad sign\n";
			exit();
		}
		
		$f=@fopen(base_path()."/public/pay/order.txt","a+") or die("error");
		while(!feof($f)){
			$str=fgets($f);

			$str_exp = explode(";", $str);
			if ($str_exp[0]=="order_num :$inv_id"){ 
				return "Операция прошла успешно\n";
			}
		}
		fclose($f);
		
		return "Произошла ошибка\n";
	}
	
	
	
	public function fail()
	{
		$inv_id = $_REQUEST["InvId"];
		return "Вы отказались от оплаты. Заказ# $inv_id\n";
	}
	
}
