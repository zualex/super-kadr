<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class AddFieldPlaylist extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('playlists', function($table)
		{
			$table->integer('idblock')->default(0)->index();
		});
		Schema::table('playlist_times', function($table)
		{
			$table->integer('idblock')->default(0)->index();
		});
	}
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('playlists', function($table)
		{
			$table->dropIndex('playlists_idblock_index');
			$table->dropColumn('idblock');
		});
		
		Schema::table('playlist_times', function($table)
		{
			$table->dropIndex('playlist_times_idblock_index');
			$table->dropColumn('idblock');
		});
	}
}