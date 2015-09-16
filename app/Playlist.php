<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use SoapBox\Formatter\Formatter;
use File;

class Playlist extends Model {

	public $error;
	public $pathPlaylist;
	
	public function __construct(){
		$this->error = array();
		$this->pathPlaylist = base_path()."/resources/playlistFiles";
	}
	
	/*
	* Загрузка исходног файла в БД
	*/
	public function initFile(){
		$contents = File::get($this->pathPlaylist.'/test.xjob');
		$formatter = Formatter::make($contents, Formatter::XML);
		$arrContent   = $formatter->toArray();

		foreach($arrContent['collection']['item'] as $key => $itemTemp){
			$item = $itemTemp['@attributes'];
			
			$type = 0;
			$enable = '';
			$name = '';
			$loop = '';
			$IsTime = '';
			$time = '';
			
			if (array_key_exists('enable', $item)) {$enable = $item['enable'];}
			if (array_key_exists('name', $item)) {$name = $item['name'];}
			if (array_key_exists('loop', $item)) {$loop = $item['loop'];}
			if (array_key_exists('IsTime', $item)) {$IsTime = $item['IsTime'];}
			if (array_key_exists('time', $item)) {$time = $item['time'];}
			
			if($enable == 'true' OR $enable == 'True'){
				$enable = 1;
			}else{
				$enable = 0;
			}
			
			if ($IsTime == 'true' OR $IsTime == 'True') {
				$IsTime = 1;
			}else{
				$IsTime = 0;
			}
			
			
			$playlist = new Playlist;
			$playlist->type = $type;
			$playlist->enable = $enable;
			$playlist->name = $name;
			$playlist->loop_xml = $loop;
			$playlist->is_time = $IsTime;
			$playlist->time = $time;
			$playlist->save();
			
		}
		
	}
	
}
