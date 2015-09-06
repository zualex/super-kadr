<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTableGallery extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		* monitors
		*/
		Schema::table('monitors', function($table)
		{
			$table->dropColumn('id_monitor');
			$table->integer('number')->index();
		});
		
		/*
		* galleries drop tarif_id
		*/
		Schema::table('galleries', function($table)
		{
			$table->dropForeign('galleries_tarif_id_foreign');
			$table->dropColumn('tarif_id');
		});
		
		/*
		* pays
		*/
		Schema::table('pays', function($table)
		{
			$table->integer('tarif_id')->unsigned();
			$table->foreign('tarif_id')->references('id')->on('tarifs');
			$table->integer('monitor_id')->unsigned();
			$table->foreign('monitor_id')->references('id')->on('monitors');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		/*
		* monitors
		*/
		Schema::table('monitors', function($table)
		{
			$table->dropIndex('monitors_number_index');
			$table->dropColumn('number');
			$table->integer('id_monitor');
		});
		
		
		/*
		* galleries add tarif_id
		*/
		Schema::table('galleries', function($table)
		{
			$table->integer('tarif_id')->unsigned();
			$table->foreign('tarif_id')->references('id')->on('tarifs');
		});
		
		/*
		* pays drop tarif_id
		*/
		Schema::table('pays', function($table)
		{
			$table->dropForeign('pays_tarif_id_foreign');
			$table->dropColumn('tarif_id');
			$table->dropForeign('pays_monitor_id_foreign');
			$table->dropColumn('monitor_id');
		});
	}

}
