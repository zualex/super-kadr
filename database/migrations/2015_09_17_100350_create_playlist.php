<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylist extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('playlists', function(Blueprint $table)
		{
			$table->increments('id');
			$table->tinyInteger('enable')->default(0);
			$table->string('name');
			$table->integer('loop_xml')->default(0);
			$table->tinyInteger('is_time')->default(0);
			$table->integer('time')->default(0);
			$table->tinyInteger('type')->default(0);	//0 - исходный 1 - новый сформированный
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
		Schema::dropIfExists('playlists');
	}

}
