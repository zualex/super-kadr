<?php namespace App\Http\Controllers;

use Auth;

use App\User;
use App\Gallery;
use App\Tarif;
use App\Monitor;
use Carbon\Carbon;



class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index(User $userModel, Gallery $galleryModel)
	{
		$data = array(
			'mainGallery' => $galleryModel->mainGallery(),
			'dateContent' => $galleryModel->dateContent(),
			'tarifs' => Tarif::all(),
			'paramMonitor' => Monitor::all(),
		);
		
		//dd($galleryModel->mainGallery());
		return view('home')->with('data', $data);

	}

}
