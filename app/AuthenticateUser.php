<?php namespace App;

use Illuminate\Contracts\Auth\Guard;
use Laravel\Socialite\Contracts\Factory as Socialite;
use App\Repositories\UserRepository;

class AuthenticateUser {
	
	private $users;
	private $socialite;
	private $auth;
	
	
	public function __construct(UserRepository $users, Socialite $socialite, Guard $auth)
	{
		$this->users = $users;
		$this->socialite = $socialite;
		$this->auth = $auth;
		
	}
	
	public function execute($hasCode)
	{
		if(!$hasCode){return $this->getAuthorizationFirst();}
		
		$user = $this->socialite->driver('github')->user();
		dd($user);
	}
	
	
	public function getAuthorizationFirst()
	{
		return $this->socialite->driver('github')->redirect();
	}
}