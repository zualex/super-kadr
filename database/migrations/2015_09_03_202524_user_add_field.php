<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAddField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('user_socials');
		Schema::table('users', function($table)
		{
			$table->string('provider', 100)->index();
			$table->string('social_id')->index();
			$table->string('avatar');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('user_socials', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('provider', 100)->index();
			$table->string('social_id')->index();
			$table->string('name');
			$table->string('avatar');
			$table->integer('user_id')->index();
			$table->timestamps();
		});
		
		Schema::table('users', function($table)
		{
			$table->dropColumn(['provider', 'social_id', 'avatar']);
		});
	}

}
