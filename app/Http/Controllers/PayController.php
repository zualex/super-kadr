<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Redirect;
use Auth;

class PayController extends Controller {


	public function index()
	{
		$mrh_login = env('ROBOKASSA_LOGIN');
		$mrh_pass1 = env('ROBOKASSA_PASSWORD_1');


		$inv_id = 0;
		$inv_desc = "ROBOKASSA Advanced User Guide";
		$out_summ = "150";
		$in_curr = "";
		$culture = "ru";
		$Shp_user = Auth::user()->id;
		
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
		$Shp_user = $_REQUEST["Shp_user"];
		$crc = $_REQUEST["SignatureValue"];
		$crc = strtoupper($crc);

		$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass1:Shp_user=$Shp_user"));
		
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
