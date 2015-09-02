<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'App\User',
		'secret' => '',
	],
	
	//Twitter https://apps.twitter.com/app/8740000
	'twitter' => [
		'client_id' => 't3O4iFG6Dr9EIl54Rld5POdHp',
		'client_secret' => 'WNKnYcJMGmq5tB0Kqtj5YO4w3a6tfyjWAUo4pMZd7sUi1AXQUA',
		'redirect' => 'http://banner-web.local/login/callback/twitter',
	],
	
	//github  https://github.com/settings/applications/239069
	/*'github' => [
		'client_id' => '0ea1e2e1fe2c9cc32ce3',
		'client_secret' => 'e31323a58424e3493817e929154f1b261c0b624a',
		'redirect' => 'http://banner-web.local/login/callback/github',
	],*/
	
	//facebook  https://developers.facebook.com/apps/1690710344477613/dashboard/
	'facebook' => [
		'client_id' => '1690710344477613',
		'client_secret' => '7d0922387e821f652e3283902d1a0c0c',
		'redirect' => 'http://banner-web.local/login/callback/facebook',
	],
	
	
	//vkontakte http://vk.com/editapp?id=5055126&section=options
	'vkontakte' => [
		'client_id' => '5055126',
		'client_secret' => 'K26XMIpmnE0pVEk2WbnQ',
		'redirect' => 'http://banner-web.local/login/callback/vkontakte',  
	],
	
	'odnoklassniki' => [
		'client_id' => '1111111111',
		'client_secret' => '1111111111',
		'redirect' => 'http://banner-web.local/login/callback/odnoklassniki',  
	],
	
	

];
