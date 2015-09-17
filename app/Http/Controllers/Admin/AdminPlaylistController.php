<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Response;

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
	
	
	// удаление записи
	public function delete($id){
		$playlist = Playlist::find($id);
		if($playlist){
			$playlist->delete();
			Session::flash('message', 'Запись удалена');
		}
		return redirect()->route('admin.playlist.index');
	}
	
	//Изменение состояния
	public function enable($id){
		$playlist = Playlist::find($id);
		$enable = Request::input('value');
		if($playlist){
			$playlist->enable = $enable;
			$playlist->save();
		
			$res = array(
				"status" => 'success',
				"message" => 'Изменения сохранены'
			);
		}else{
			$res = array(
				"status" => 'error',
				"message" => 'Произошла ошибка. Изменения не сохранены'
			);
		}

		
		return Response::json($res);
	}
	
	
	//Изменение IsTime
	public function isTime($id){
		$playlist = Playlist::find($id);
		$isTime = Request::input('value');
		if($playlist){
			$playlist->is_time = $isTime;
			$playlist->save();
		
			$res = array(
				"status" => 'success',
				"message" => 'Изменения сохранены'
			);
		}else{
			$res = array(
				"status" => 'error',
				"message" => 'Произошла ошибка. Изменения не сохранены'
			);
		}

		
		return Response::json($res);
	}
	
	
	

}
