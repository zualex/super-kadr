<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixFieldTransaction extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pays', function($table)
		{
			$table->tinyInteger('visible')->default(1);
			
			
			$table->dropForeign('pays_tarif_id_foreign');
			$table->dropColumn('tarif_id');
			$table->dropForeign('pays_monitor_id_foreign');
			$table->dropColumn('monitor_id');
			$table->dropColumn('date_show');
		});
		
		Schema::table('galleries', function($table)
		{
			$table->integer('count_show');
			$table->dateTime('date_show');
			$table->integer('tarif_id')->unsigned()->nullable();
			$table->foreign('tarif_id')->references('id')->on('tarifs');
			$table->integer('monitor_id')->unsigned()->nullable();
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
		Schema::table('pays', function($table)
		{
			$table->dropColumn('visible');
			
			
			$table->dateTime('date_show');
			$table->integer('tarif_id')->unsigned()->nullable();
			$table->foreign('tarif_id')->references('id')->on('tarifs');
			$table->integer('monitor_id')->unsigned()->nullable();
			$table->foreign('monitor_id')->references('id')->on('monitors');
		});
		
		Schema::table('galleries', function($table)
		{
			$table->dropForeign('galleries_tarif_id_foreign');
			$table->dropColumn('tarif_id');
			$table->dropForeign('galleries_monitor_id_foreign');
			$table->dropColumn('monitor_id');
			$table->dropColumn('date_show');
			$table->dropColumn('count_show');
		});
	}

}
