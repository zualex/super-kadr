<?php namespace App\Http\Middleware;

use Closure;
use Auth;
use Request;
use App\Setting;
use Session;
use App\UserIp;

class BlockUserIp {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		
		//$userIp = new UserIp;
		//$userIp->badLogin();
		
		$userIp = new UserIp;		
		if($userIp->checkBlock()){
			return redirect()->route('main');
		}
		
		return $next($request);
		
	}

}
