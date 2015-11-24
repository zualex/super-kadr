<?php namespace App\Handlers\Events;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use App\User;
use Auth;
use Request;
use Carbon\Carbon;
use Mail;
use App\Setting;

class AuthLoginEventHandler {

    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  User $user
     * @param  $remember
     * @return void
     */
    public function handle(User $user, $remember)
    {
		if (Auth::check() && Auth::user()->level == 'admin' || Auth::user()->level == 'moderator'){
			$settingModel = new Setting;
			$result = $settingModel->getSendEmails();
			if(count($result) > 0){
				if($result->value != ''){				
					$key = Array(
						"name" => Auth::user()->name,
						"email" => Auth::user()->email,
						"time" => Carbon::now()->toDateTimeString(),
						"ip" => Request::getClientIp(),
					);
					$emails = explode(',', $result->value);
					Mail::send('mail.signAdminPanel', ['key' => $key], function($message) use ($emails) 
					{
						foreach($emails as $email){
							$message->to(trim($email))->subject('Уведомление о входе в административную панель');
						}
					});
				}
			}
		}
    }

}