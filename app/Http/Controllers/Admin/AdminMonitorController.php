<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use Session;
use File;
use Response;
use Request;
use Input;

use App\Monitor;


class AdminMonitorController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = array(
			"monitor1" => Monitor::where('number', '=', 1)->first(),
			"monitor2" => Monitor::where('number', '=', 2)->first(),
		);
		return view('admin.monitor.index')->with('data', $data);
	}
	
	
	public function success()
	{
		$resAll = Request::except('_token');
		$result = 1;		
		
		$siteWidth1 = '';
		$siteWidth2 = '';
		$siteHeight1 = '';
		$siteHeight2 = '';
		$origWidth1 = '';
		$origWidth2 = '';
		$origHeight1 = '';
		$origHeight2 = '';
		$mediumWidth1 = '';
		$mediumWidth2 = '';
		$mediumHeight1 = '';
		$mediumHeight2 = '';
		$smallWidth1 = '';
		$smallWidth2 = '';
		$smallHeight1 = '';
		$smallHeight2 = '';
		
		
		if(array_key_exists('siteWidth1', $resAll)){$siteWidth1 = $resAll['siteWidth1'];}else{$result = 0;}
		if(array_key_exists('siteWidth2', $resAll)){$siteWidth2 = $resAll['siteWidth2'];}else{$result = 0;}
		if(array_key_exists('siteHeight1', $resAll)){$siteHeight1 = $resAll['siteHeight1'];}else{$result = 0;}
		if(array_key_exists('siteHeight2', $resAll)){$siteHeight2 = $resAll['siteHeight2'];}else{$result = 0;}
		if(array_key_exists('origWidth1', $resAll)){$origWidth1 = $resAll['origWidth1'];}else{$result = 0;}
		if(array_key_exists('origWidth2', $resAll)){$origWidth2 = $resAll['origWidth2'];}else{$result = 0;}
		if(array_key_exists('origHeight1', $resAll)){$origHeight1 = $resAll['origHeight1'];}else{$result = 0;}
		if(array_key_exists('origHeight2', $resAll)){$origHeight2 = $resAll['origHeight2'];}else{$result = 0;}
		if(array_key_exists('mediumWidth1', $resAll)){$mediumWidth1 = $resAll['mediumWidth1'];}else{$result = 0;}
		if(array_key_exists('mediumWidth2', $resAll)){$mediumWidth2 = $resAll['mediumWidth2'];}else{$result = 0;}
		if(array_key_exists('mediumHeight1', $resAll)){$mediumHeight1 = $resAll['mediumHeight1'];}else{$result = 0;}
		if(array_key_exists('mediumHeight2', $resAll)){$mediumHeight2 = $resAll['mediumHeight2'];}else{$result = 0;}
		if(array_key_exists('smallWidth1', $resAll)){$smallWidth1 = $resAll['smallWidth1'];}else{$result = 0;}
		if(array_key_exists('smallWidth2', $resAll)){$smallWidth2 = $resAll['smallWidth2'];}else{$result = 0;}
		if(array_key_exists('smallHeight1', $resAll)){$smallHeight1 = $resAll['smallHeight1'];}else{$result = 0;}
		if(array_key_exists('smallHeight2', $resAll)){$smallHeight2 = $resAll['smallHeight2'];}else{$result = 0;}
		
		
		
		if($result == 1){
			
			$monitor1 = Monitor::where('number', '=', 1)->first();
			$monitor1->siteWidth = $siteWidth1;
			$monitor1->siteHeight = $siteHeight1;
			$monitor1->origWidth = $origWidth1;
			$monitor1->origHeight = $origHeight1;
			$monitor1->mediumWidth = $mediumWidth1;
			$monitor1->mediumHeight = $mediumHeight1;
			$monitor1->smallWidth = $smallWidth1;
			$monitor1->smallHeight = $smallHeight1;
			$monitor1->save();
			
			
			
			$monitor2 = Monitor::where('number', '=', 2)->first();
			$monitor2->siteWidth = $siteWidth2;
			$monitor2->siteHeight = $siteHeight2;
			$monitor2->origWidth = $origWidth2;
			$monitor2->origHeight = $origHeight2;
			$monitor2->mediumWidth = $mediumWidth2;
			$monitor2->mediumHeight = $mediumHeight2;
			$monitor2->smallWidth = $smallWidth2;
			$monitor2->smallHeight = $smallHeight2;
			$monitor2->save();
			
			
			
			
			Session::flash('message', 'Настройки сохранены');
			$res = array(
				"status" => 'success',
				"message" => 'Настройки сохранены'
			);
		}else{
			$res = array(
				"status" => 'error',
				"message" => 'Произошла ошибка при сохранении'.$siteWidth1
			);
		}

		return Response::json($res);
	}


}
