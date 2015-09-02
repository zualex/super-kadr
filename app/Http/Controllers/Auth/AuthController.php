<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Socialize;
use Illuminate\Http\Request;
use App\AuthenticateUser;
 use Laravel\Socialite\Contracts\Factory as Socialite;

class AuthController extends Controller {
	
	
	
	protected $redirectPath = '/';
	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar, Socialite $socialite)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;
		
		$this->socialite = $socialite;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

	
	 public function getSocialAuth($provider=null)
	{
	   if(!config("services.$provider")) abort('404'); //just to handle providers that doesn't exist

	   return $this->socialite->with($provider)->redirect();
	}


	public function getSocialAuthCallback($provider=null)
	{
		if($user = $this->socialite->with($provider)->user()){
			
			/*
			$user->getId();
			$user->getNickname();
			$user->getName();
			$user->getEmail();
			$user->getAvatar();
			*/
			
			
			
			dd($user->getNickname());
		}else{
			return 'something went wrong';
		}
	}

	
	
}




