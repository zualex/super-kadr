<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Tarif;
use App\Status;
use App\Monitor;
use App\Setting;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
		
		$this->call('MonitorTableSeeder');
		$this->call('TarifTableSeeder');
		$this->call('StatusTableSeeder');
		
	}

}


/*
* Monitor
*/
class MonitorTableSeeder extends Seeder {

  public function run()
  {
    DB::table('monitors')->delete();

    Monitor::create([
		'id' => '1',
		'number' => '1',
		'siteWidth' => '480',
		'siteWidth' => '480',
		'siteHeight' => '480',
		'origWidth' => '1280',
		'origHeight' => '1280',
		'mediumWidth' => '240',
		'mediumHeight' => '240',
		'smallWidth' => '140',
		'smallHeight' => '140',
	]);
	
	Monitor::create([
		'id' => '2',
		'number' => '2',
		'siteWidth' => '740',
		'siteHeight' => '480',
		'origWidth' => '2240',
		'origHeight' => '1453',
		'mediumWidth' => '370',
		'mediumHeight' => '240',
		'smallWidth' => '216',
		'smallHeight' => '140',
	]);
	
  }

}



/*
*	 Tarif
*/
class TarifTableSeeder extends Seeder {

  public function run()
  {
    DB::table('tarifs')->delete();

    Tarif::create([
		'id' => '1',
		'name' => 'Просто',
		'desc_main' => '12 показов',
		'desc_dop' => 'в течение 1 часа',
		'hours' => '1',
		'interval_sec' => '300',
		'price' => '150',
	]);
	
	Tarif::create([
		'id' => '2',
		'name' => 'Забавно',
		'desc_main' => '4 показа',
		'desc_dop' => 'в течение 5 часов',
		'hours' => '4',
		'interval_sec' => '900',
		'price' => '200',
	]);
	
	Tarif::create([
		'id' => '3',
		'name' => 'Весело',
		'desc_main' => '2 показа',
		'desc_dop' => 'в течение суток',
		'hours' => '24',
		'interval_sec' => '1800',
		'price' => '300',
	]);
	
  }

}


/*
* Status
*/
class StatusTableSeeder extends Seeder {

  public function run()
  {
    DB::table('statuses')->delete();
	
	Status::create([
		'id' => '1',
		'type_status' => 'pay',
		'name' => 'Оплачено',
		'caption' => 'paid',
	]);
	Status::create([
		'id' => '2',
		'type_status' => 'pay',
		'name' => 'Ожидание оплаты',
		'caption' => 'wait',
	]);
	Status::create([
		'id' => '3',
		'type_status' => 'pay',
		'name' => 'Ошибка',
		'caption' => 'Error',
	]);
	Status::create([
		'id' => '4',
		'type_status' => 'pay',
		'name' => 'Отклонено пользователем',
		'caption' => 'cancelUser',
	]);
	Status::create([
		'id' => '5',
		'type_status' => 'pay',
		'name' => 'Отклонено администратором',
		'caption' => 'cancelAdmin',
	]);
	
	
	Status::create([
		'id' => '6',
		'type_status' => 'main',
		'name' => 'Одобрено',
		'caption' => 'success',
	]);
	Status::create([
		'id' => '7',
		'type_status' => 'main',
		'name' => 'На модерации',
		'caption' => 'moderation',
	]);
	Status::create([
		'id' => '8',
		'type_status' => 'main',
		'name' => 'Отклонено',
		'caption' => 'cancel',
	]);
	
	
    Status::create([
		'id' => '9',
		'type_status' => 'order',
		'name' => 'Выполнена',
		'caption' => 'success',
	]);
	Status::create([
		'id' => '10',
		'type_status' => 'order',
		'name' => 'В исполнении',
		'caption' => 'process',
	]);
	Status::create([
		'id' => '11',
		'type_status' => 'order',
		'name' => 'В очереди на исполнение',
		'caption' => 'queue',
	]);

  }

}