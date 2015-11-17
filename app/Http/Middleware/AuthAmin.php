<?php namespace App\Http\Middleware;
use Closure;
use Auth;
class AuthAmin {

	/**
	* Handle an incoming request.
	*
	* @param \Illuminate\Http\Request $request
	* @param \Closure $next
	* @return mixed
	*/

	public function handle($request, Closure $next)
	{
		if (Auth::check()){
			if (Auth::user()->level == 'admin' || Auth::user()->level == 'moderator'){
				return $next($request);
			}
		}
		return redirect()->guest('auth/login');

	}
}