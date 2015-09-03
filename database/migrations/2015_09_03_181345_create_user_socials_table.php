<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSocialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_socials', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('provider', 100)->index();
			$table->string('social_id')->index();
			$table->string('name');
			$table->string('avatar');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_socials');
	}

}
