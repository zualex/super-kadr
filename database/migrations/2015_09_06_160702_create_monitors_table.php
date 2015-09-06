<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('monitors', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('count_monitor');
			$table->integer('siteWidth');
			$table->integer('siteHeight');
			$table->integer('origWidth');
			$table->integer('origHeight');
			$table->integer('mediumWidth');
			$table->integer('mediumHeight');
			$table->integer('smallWidth');
			$table->integer('smallHeight');
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
		Schema::drop('monitors');
	}

}
