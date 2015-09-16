<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Playlist;

class AdminPlaylistController extends Controller {


	public function index(Playlist $playlistModel)
	{
		$playlistModel->initFile();
		return view('admin.playlist.index');
	}

	

}
