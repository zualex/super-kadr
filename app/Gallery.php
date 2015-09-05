<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Hash;
use Session;
use Auth;

class Gallery extends Model {

	/*
	*	Значение для загрузки картинки
	* Одноразовые сессии
	*/
	public function sessionUpload(){
		$value = '';
		if (Auth::check()){
			$value = str_random(30);
			Session::flash('uploadImg', $value);
		}
		return $value;
	}
	
	
	
	
	public function dateContent(){
		$day_of_week = date('N');
		function day_of_week($day) {
			switch ($day) {
				case ($day == 1 || $day == 8 || $day == 15):
					$return = "Понедельник";
					break;
				case ($day == 2 || $day == 9 || $day == 16):
					$return = "Вторник";
					break;
				case ($day == 3 || $day == 10 || $day == 17):
					$return = "Среда";
					break;
				case ($day == 4 || $day == 11 || $day == 18):
					$return = "Четверг";
					break;
				case ($day == 5 || $day == 12 || $day == 19):
					$return = "Пятница";
					break;
				case ($day == 6 || $day == 13 || $day == 20):
					$return = "Суббота";
					break;
				case ($day == 7 || $day == 14 || $day == 21):
					$return = "Воскресенье";
					break;		
			}
			return $return;
		}
		function month ($month) {
			switch ($month) {
				case 1:
					$return = "Января";
					break;
				case 2:
					$return = "Февраля";
					break;
				case 3:
					$return = "Марта";
					break;
				case 4:
					$return = "Апреля";
					break;
				case 5:
					$return = "Мая";
					break;
				case 6:
					$return = "Июня";
					break;
				case 7:
					$return = "Июля";
					break;
				case 8:
					$return = "Августа";
					break;	
				case 9:
					$return = "Сентября";
					break;	
				case 10:
					$return = "Октября";
					break;	
				case 11:
					$return = "Ноября";
					break;	
				case 12:
					$return = "Декабря";
					break;		
			}
			return $return;
		}
		$content = '';
		$content_date ='';
		$content_time ='';
		$i = 0;

		$N = date('N');
		$ii = 0;
		$date = time();
		for ($j = 0; $j < 15; $j++){
			$times = '';
			$date = date("d-m-Y", time() + $j * 24 * 60 * 60);
			$content_date .= '<div class="tab-head day"><div><span class="label-h1">'.day_of_week($N + $j).'</span><span class="label-h2">'.date("j", strtotime($date)).' '.month(date("n", strtotime($date))).'</span></div></div>';
			for ($i = 0; $i < 24; $i++){
				$t = str_pad($i, 2, '0', STR_PAD_LEFT).':00';
				$time = date("$t d.m.Y", strtotime($date));
				$stime = strtotime($time);
				if ($stime < time() + 60 * 60)$status = ' deny';
				else $status = ' active';
				$times .= '<span class="time-item'.$status.'" data-time="'.$time.'" data-time2="'.strtotime($time).'">'.$t.'</span>';
			}
			$content_time .= '<section>'.$times.'</section>';
		}
		$content = '<div class="tabs">'.$content_date.'</div><div class="box-content">'.$content_time.'</div>';
		return $content;
	}

}
