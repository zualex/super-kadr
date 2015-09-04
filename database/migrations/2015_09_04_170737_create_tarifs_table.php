<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarifsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tarifs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('desc_main');
			$table->string('desc_dop');
			$table->integer('hours');
			$table->integer('interval_sec');
			$table->double('price');
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
		Schema::drop('tarifs');
	}

}
