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
use App\UserIp;
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
		$this->middleware('block.user.ip');
	}
	
	
	public function getRegister(){return redirect()->route('main');}
	public function postRegister(){return redirect()->route('main');}
	
	
	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postLogin(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email', 'password' => 'required',
		]);

		$credentials = $request->only('email', 'password');

		if ($this->auth->attempt($credentials, $request->has('remember')))
		{
			return redirect()->intended($this->redirectPath());
		}
		
		/* Сохраняем не удавшуюся попытку входа */
		$userIp = new UserIp;
		$userIp->badLogin();

		return redirect($this->loginPath())
					->withInput($request->only('email', 'remember'))
					->withErrors([
						'email' => $this->getFailedLoginMessage(),
					]);
	}
	
	
	
	
	 public function getSocialAuth($provider=null)
	{
	   if(!config("services.$provider")) abort('404'); //just to handle providers that doesn't exist
	   return $this->socialite->with($provider)->redirect();
	}
	public function getSocialAuthCallback(User $postUser, $provider=null)
	{
		try
		{
			$user = $this->socialite->with($provider)->user();
		}
		catch(\Exception $e)
		{
			return redirect()->route('main');
		}
		
			
		$name = $user->getNickname();
		if($provider == 'vkontakte'){
			if($user->email != ''){$name = $user->email;}
			if($user->name != ''){$name = $user->name;}
		}
		
		if($provider == 'facebook'){
			if($user->email != ''){$name = $user->email;}
			if($user->name != ''){$name = $user->name;}
		}
		
		if($provider == 'twitter'){
			if($user->email != ''){$name = $user->email;}
			if($user->name != ''){$name = $user->name;}
		}
		
		if($provider == 'odnoklassniki'){
			if($user->email != ''){$name = $user->email;}
			if($user->name != ''){$name = $user->name;}
		}
		
		$avatar = $user->getAvatar();
		if(array_key_exists('avatar_original', $user)){
			$avatar = $user->avatar_original;
			
			if($provider == 'facebook'){
				$avatar = str_replace('?width=1920', '?width=200', $avatar);
			}
			
		}
		
		//dd(u)
		$arrValues = array(
			'provider' => $provider,
			'social_id' => $user->getId(),
			'name' => $name,
			'email' => $user->getEmail(),
			'avatar' => $avatar,
		);
		
		$userId = $postUser->socialAuth($arrValues);
		if($userId){Auth::loginUsingId($userId);}
		
		if(count($postUser->errors) > 0){	
			return redirect()->back()->withErrors($postUser->errors);
		}else{
			return redirect()->back();
		}
	}
	
	
}