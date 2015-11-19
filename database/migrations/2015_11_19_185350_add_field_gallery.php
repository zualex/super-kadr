<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldGallery extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('galleries', function($table)
		{
			$table->dateTime('start_moderation');
			$table->dateTime('end_moderation');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('galleries', function($table)
		{
			$table->dropColumn('start_moderation');
			$table->dropColumn('end_moderation');
		});
	}

}
