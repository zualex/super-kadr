<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistTimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('playlist_times', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('number');
			$table->tinyInteger('complete')->default(0);
			$table->dateTime('dateStart');
			$table->dateTime('dateEnd');
			$table->integer('monitor_id')->unsigned()->nullable();
			$table->foreign('monitor_id')->references('id')->on('monitors');
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
		Schema::drop('playlist_times');
	}

}
