<?php namespace App\Http\Middleware;

use Closure;
use Auth;
use Request;
use App\Setting;

class SiteOff {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$setting = new Setting;
		$siteOff = $setting->siteOff();
		//dd($siteOff);
		if($siteOff == 1){
			
			/* Если не авторизован и если авторизован но не админ то кидаем на страницу */
			if (!Auth::check() OR (Auth::check() AND Auth::user()->level != 'admin')){
				switch ($request->url()) {
					case route('dev'):
						break;
					case Request::root().'/auth/login':
						break;
					case Request::root().'/auth/logout':
						break;
					default:
					   return redirect()->route('dev');
				}
			}
		}

		return $next($request);
		
	}

}
