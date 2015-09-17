<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Playlist;
use Session;

class AdminPlaylistController extends Controller {


	public function index(Playlist $playlistModel)
	{

		$data = array(
			'initPlaylist' => $playlistModel->getInitPlaylist()
		);
		return view('admin.playlist.index')->with('data', $data);
	}

	
	public function initFile()
	{
		$playlist = new Playlist;
		return $playlist->initFile();
	}
	
	
	public function delete($id){
		$playlist = Playlist::find($id);
		if($playlist){
			$playlist->delete();
			Session::flash('message', 'Запись удалена');
		}
		return redirect()->route('admin.playlist.index');
	}
	
	

}
