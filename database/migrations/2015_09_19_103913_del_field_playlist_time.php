<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DelFieldPlaylistTime extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('playlist_times', function($table)
		{
			$table->dropColumn('number');
			$table->dropColumn('complete');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('playlist_times', function($table)
		{
			$table->integer('number');
			$table->tinyInteger('complete')->default(0);
		});
	}

}
