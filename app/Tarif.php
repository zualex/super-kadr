<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model {

	public function getParamMonitor(){
		$arrMonitor = array(
			'1' => array(
				'siteWidth' => '480',
				'siteHeight' => '480',

				'origWidth' => '1280',
				'origHeight' => '1280',
				
				'mediumWidth' => '240',
				'mediumHeight' => '240',
				
				'smallWidth' => '140',
				'smallHeight' => '140',
			),
			
			'2' => array(
				'siteWidth' => '740',
				'siteHeight' => '480',
				
				'origWidth' => '2240',
				'origHeight' => '1453',
				
				'mediumWidth' => '370',
				'mediumHeight' => '240',
				
				'smallWidth' => '216',
				'smallHeight' => '140',
			),
		);
		return $arrMonitor;
	}

}
