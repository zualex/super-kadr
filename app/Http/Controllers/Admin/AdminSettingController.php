<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Setting;
use Response;
use Request;
use Session;

class AdminSettingController extends Controller {


	public function index(Setting $settingModel)
	{
		//$settingModel->createSettingDefault();
		$data = array(
			"settingMain" => $settingModel->getSettingMain(),
			"settingPay" => $settingModel->getSettingPay(),
			"settingUser" => $settingModel->getSettingUser(),
		);		
		return view('admin.setting.index')->with('data', $data);
	}

	
	public function successAll(Setting $settingModel)
	{

		$result = $settingModel->saveSetting(Request::except('_token'));
		
		if($result == '1'){
			Session::flash('message', 'Настройки сохранены');
			$res = array(
				"status" => 'success',
				"message" => 'Настройки сохранены'
			);
		}else{
			$res = array(
				"status" => 'error',
				"message" => 'Произошла ошибка при сохранении'
			);
		}

		return Response::json($res);
	}
	

}
