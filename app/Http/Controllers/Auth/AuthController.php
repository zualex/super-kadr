<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Socialize;
use Illuminate\Http\Request;
use App\AuthenticateUser;
use Laravel\Socialite\Contracts\Factory as Socialite;
 

use App\User;
use Auth;

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

	
	
	public function getRegister(){return redirect()->route('main');}
	public function postRegister(){return redirect()->route('main');}

	
	
	
	
	 public function getSocialAuth($provider=null)
	{
	   if(!config("services.$provider")) abort('404'); //just to handle providers that doesn't exist
	   return $this->socialite->with($provider)->redirect();
	}


	public function getSocialAuthCallback(User $postUser, $provider=null)
	{
		
		if($user = $this->socialite->with($provider)->user()){
			
			
			$arrValues = array(
				'provider' => $provider,
				'social_id' => $user->getId(),
				'name' => $user->getNickname(),
				'email' => $user->getEmail(),
				'avatar' => $user->getAvatar(),
			);
			
			$userId = $postUser->socialAuth($arrValues);
			if($userId){Auth::loginUsingId($userId);}
			
			if(count($postUser->errors) > 0){	
				return redirect()->back()->withErrors($postUser->errors);
			}else{
				return redirect()->back();
			}

			
		}else{
			return 'something went wrong';
		}
	}

	
	
}




