<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('galleries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('tarif_id')->unsigned();
			$table->foreign('tarif_id')->references('id')->on('tarifs');
			$table->string('src');
						
			$table->integer('status_main')->unsigned();
			$table->foreign('status_main')->references('id')->on('statuses');
			
			$table->integer('status_order')->unsigned();
			$table->foreign('status_order')->references('id')->on('statuses');
			
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
		Schema::drop('galleries');
	}

}
