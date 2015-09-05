<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model {

	public function getParamMonitor(){
		$arrMonitor = array(
			'1' => array(
				'siteWidth' => '520',
				'siteHeight' => '520',

				'origWidth' => '1280',
				'origHeigh' => '1280',
				
				'mediumWidth' => '240',
				'mediumHeigh' => '240',
				
				'smallWidth' => '140',
				'smallHeigh' => '140',
			),
			
			'2' => array(
				'siteWidth' => '780',
				'siteHeight' => '520',
				
				'origWidth' => '2240',
				'origHeigh' => '1493',
				
				'mediumWidth' => '360',
				'mediumHeigh' => '240',
				
				'smallWidth' => '210',
				'smallHeigh' => '140',
			),
		);
		return $arrMonitor;
	}

}
