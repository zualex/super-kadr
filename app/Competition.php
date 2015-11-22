<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model {


	/*
	*	Все условия конкурсов
	*	Ключи не менять, описание можно менять
	*/
	public function getCondition(){
		return array(
			'like_all_foto' => 'Макс кол-во лайков по всем фото клиента',
			'like_one_foto' => 'Макс кол-во лайков на одну фото клиента',
			'foto_all_user' => 'Макс кол-во фото у одного клиента',
		);
	}

}
