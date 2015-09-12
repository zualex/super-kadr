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
	
	//Test Twitter https://apps.twitter.com/app/8740000
	//Twitter https://apps.twitter.com/app/8749222
	'twitter' => [
		'client_id' => '',
		'client_secret' => '',
		'redirect' => '',
	],
	
	
	//Test facebook  https://developers.facebook.com/apps/1690710344477613/dashboard/
	//facebook  https://developers.facebook.com/apps/171355216531108/
	'facebook' => [
		'client_id' => '',
		'client_secret' => '',
		'redirect' => '',
	],
	
	
	//Test vkontakte http://vk.com/editapp?id=5055126&section=options
	//vkontakte http://vk.com/editapp?id=5057233&section=options
	'vkontakte' => [
		'client_id' => '',
		'client_secret' => '',
		'redirect' => '',
	],
	
	'odnoklassniki' => [
		'client_id' => '',
		'client_secret' => '',
		'redirect' => '',
	],
	
	

];
