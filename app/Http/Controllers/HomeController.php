<?php namespace App\Http\Controllers;

use Auth;

use App\User;
use App\Gallery;



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
			'dateContent' => $galleryModel->dateContent(),
			'sessionUpload' => $galleryModel->sessionUpload(),
		);
		
		return view('home')->with('data', $data);

	}

}
