@extends('admin.app')

@section('content')
<div id="sett">
	<div class="header">
		<div class="clear">
			<div class="title"><span>Настройки</span></div>
			<div class="controls">
				<div class="btn-group">
					<a href="" class="btn add savesettings"><i class="fa pull-left fa-floppy-o"></i>Сохранить</a>
				</div>
			</div>
		</div>
	</div>
	<div class="site-settings tab-block">
		<div class="settings tabs">
			<ul>
				<li class="active">Основные</li>
				<li>Платежи</li>
				<li>Пользователи</li>
			</ul>
		</div>
		<div class="box-content">
			<section id="general" class="visible">
				@if(count($data['settingMain']) > 0)
					@foreach($data['settingMain'] as $key => $value)
						<div class="inline-block">
							<div class="line-title"><span>{{ $value->caption }}</span></div>
							<div class="line-value"><input class="inputbox" type="text" name="{{ $value->name }}" value="{{ $value->value }}" placeholder="Название {{ $value->value }}"></div>
						</div>
					@endforeach
				@endif
			
				<div class="inline-block">
					<div class="line-title"><span>Название сайта</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="title" value="Супер Кадр" placeholder="Название сайта"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Кодировка сайта</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="charset" value="utf-8"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Описание (Description)</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="description" value="Описание"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Ключевые слова</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="keywords" value="Ключевые слова"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Часовой пояс</span></div>
					<div class="line-value">
						<select class="selectbox" name="time-zone">
							<option value="Pacific/Midway">(GMT-11:00) Остров Мидуэй</option>
							<option value="US/Samoa">(GMT-11:00) Самоа</option>
							<option value="US/Hawaii">(GMT-10:00) Гавайи</option>
							<option value="US/Alaska">(GMT-09:00) Аляска</option>
							<option value="US/Pacific">(GMT-08:00) Тихоокеанское время (США и Канада)</option>
							<option value="America/Tijuana">(GMT-08:00) Тихуана</option>
							<option value="US/Arizona">(GMT-07:00) Аризона</option>
							<option value="US/Mountain">(GMT-07:00) Горное время (США и Канада)</option>
							<option value="America/Chihuahua">(GMT-07:00) Чихуахуа</option>
							<option value="America/Mazatlan">(GMT-07:00) Масатлан</option>
							<option value="America/Mexico_City">(GMT-06:00) Мехико</option>
							<option value="America/Monterrey">(GMT-06:00) Монтеррей</option>
							<option value="US/Central">(GMT-06:00) Центральное время (США и Канада)</option>
							<option value="US/Eastern">(GMT-05:00) Восточное время (США и Канада)</option>
							<option value="US/East-Indiana">(GMT-05:00) Индиана (Восток)</option>
							<option value="America/Lima">(GMT-05:00) Лима, Богота</option>
							<option value="America/Caracas">(GMT-04:30) Каракас</option>
							<option value="Canada/Atlantic">(GMT-04:00) Атлантическое время (Канада)</option>
							<option value="America/La_Paz">(GMT-04:00) Ла-Пас</option>
							<option value="America/Santiago">(GMT-04:00) Сантьяго</option>
							<option value="Canada/Newfoundland">(GMT-03:30) Ньюфаундленд</option>
							<option value="America/Buenos_Aires">(GMT-03:00) Буэнос-Айрес</option>
							<option value="Greenland">(GMT-03:00) Гренландия</option>
							<option value="Atlantic/Stanley">(GMT-02:00) Стэнли</option>
							<option value="Atlantic/Azores">(GMT-01:00) Азорские острова</option>
							<option value="Africa/Casablanca">(GMT) Касабланка</option>
							<option value="Europe/Dublin">(GMT) Дублин</option>
							<option value="Europe/Lisbon">(GMT) Лиссабон</option>
							<option value="Europe/London">(GMT) Лондон</option>
							<option value="Europe/Amsterdam">(GMT+01:00) Амстердам</option>
							<option value="Europe/Belgrade">(GMT+01:00) Белград</option>
							<option value="Europe/Berlin">(GMT+01:00) Берлин</option>
							<option value="Europe/Bratislava">(GMT+01:00) Братислава</option>
							<option value="Europe/Brussels">(GMT+01:00) Брюссель</option>
							<option value="Europe/Budapest">(GMT+01:00) Будапешт</option>
							<option value="Europe/Copenhagen">(GMT+01:00) Копенгаген</option>
							<option value="Europe/Madrid">(GMT+01:00) Мадрид</option>
							<option value="Europe/Paris">(GMT+01:00) Париж</option>
							<option value="Europe/Prague">(GMT+01:00) Прага</option>
							<option value="Europe/Rome">(GMT+01:00) Рим</option>
							<option value="Europe/Sarajevo">(GMT+01:00) Сараево</option>
							<option value="Europe/Stockholm">(GMT+01:00) Стокгольм</option>
							<option value="Europe/Vienna">(GMT+01:00) Вена</option>
							<option value="Europe/Warsaw">(GMT+01:00) Варшава</option>
							<option value="Europe/Zagreb">(GMT+01:00) Загреб</option>
							<option value="Europe/Athens">(GMT+02:00) Афины</option>
							<option value="Europe/Bucharest">(GMT+02:00) Бухарест</option>
							<option value="Europe/Helsinki">(GMT+02:00) Хельсинки</option>
							<option value="Europe/Istanbul">(GMT+02:00) Стамбул</option>
							<option value="Asia/Jerusalem">(GMT+02:00) Иерусалим</option>
							<option value="Europe/Kiev">(GMT+02:00) Киев</option>
							<option value="Europe/Minsk">(GMT+02:00) Минск</option>
							<option value="Europe/Riga">(GMT+02:00) Рига</option>
							<option value="Europe/Sofia">(GMT+02:00) София</option>
							<option value="Europe/Tallinn">(GMT+02:00) Таллин</option>
							<option value="Europe/Vilnius">(GMT+02:00) Вильнюс</option>
							<option value="Asia/Baghdad">(GMT+03:00) Багдад</option>
							<option value="Asia/Kuwait">(GMT+03:00) Кувейт</option>
							<option value="Africa/Nairobi">(GMT+03:00) Найроби</option>
							<option value="Asia/Tehran">(GMT+03:30) Иран, Тегеран</option>
							<option value="Europe/Kaliningrad" selected="">(GMT+02:00) Калининград</option>
							<option value="Europe/Moscow">(GMT+03:00) Москва</option>
							<option value="Europe/Volgograd">(GMT+03:00) Волгоград</option>
							<option value="Europe/Samara">(GMT+04:00) Самара, Удмуртия</option>
							<option value="Asia/Baku">(GMT+04:00) Баку</option>
							<option value="Asia/Muscat">(GMT+04:00) Абу-Даби, Маскат</option>
							<option value="Asia/Tbilisi">(GMT+04:00) Тбилиси</option>
							<option value="Asia/Yerevan">(GMT+04:00) Ереван</option>
							<option value="Asia/Kabul">(GMT+04:30) Афганистан, Кабул</option>
							<option value="Asia/Yekaterinburg">(GMT+05:00) Екатеринбург, Пермь</option>
							<option value="Asia/Tashkent">(GMT+05:00) Ташкент, Карачи</option>
							<option value="Asia/Kolkata">(GMT+05:30) Бомбей, Калькутта, Мадрас, Нью-Дели, Коломбо</option>
							<option value="Asia/Kathmandu">(GMT+05:45) Катманду</option>
							<option value="Asia/Almaty">(GMT+06:00) Алматы, Астана</option>
							<option value="Asia/Novosibirsk">(GMT+06:00) Новосибирск</option>
							<option value="Asia/Jakarta">(GMT+07:00) Бангкок, Ханой, Джакарта</option>
							<option value="Asia/Krasnoyarsk">(GMT+07:00) Красноярск</option>
							<option value="Asia/Hong_Kong">(GMT+08:00) Гонконг, Чунцин</option>
							<option value="Asia/Kuala_Lumpur">(GMT+08:00) Куала-Лумпур</option>
							<option value="Asia/Singapore">(GMT+08:00) Сингапур</option>
							<option value="Asia/Taipei">(GMT+08:00) Тайбэй</option>
							<option value="Asia/Ulaanbaatar">(GMT+08:00) Улан-Батор</option>
							<option value="Asia/Urumqi">(GMT+08:00) Урумчи</option>
							<option value="Asia/Irkutsk">(GMT+08:00) Иркутск</option>
							<option value="Asia/Seoul">(GMT+09:00) Сеул</option>
							<option value="Asia/Tokyo">(GMT+09:00) Токио, Осака, Саппоро</option>
							<option value="Australia/Adelaide">(GMT+09:30) Аделаида</option>
							<option value="Australia/Darwin">(GMT+09:30) Дарвин</option>
							<option value="Asia/Yakutsk">(GMT+09:00) Якутск</option>
							<option value="Australia/Brisbane">(GMT+10:00) Брисбен</option>
							<option value="Pacific/Port_Moresby">(GMT+10:00) Гуам, Порт-Морсби</option>
							<option value="Australia/Sydney">(GMT+10:00) Мельбурн, Сидней, Канберра</option>
							<option value="Asia/Vladivostok">(GMT+10:00) Владивосток</option>
							<option value="Asia/Sakhalin">(GMT+11:00) Сахалин</option>
							<option value="Asia/Magadan">(GMT+12:00) Магадан, Камчатка</option>
							<option value="Pacific/Auckland">(GMT+12:00) Окленд, Веллингтон</option>
							<option value="Pacific/Fiji">(GMT+12:00) Фиджи, Маршалловы о.</option>
						</select>
					</div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Используемый язык</span></div>
					<div class="line-value">
						<select class="selectbox" name="time-zone">
							<option value="ru">Русский</option>
						</select>
					</div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Выключить сайт</span></div>
					<div class="line-value">
						<input id="toggle-1" class="toggle" name="offline" type="checkbox">
						<label for="toggle-1"></label>
					</div>
				</div>
			</section>
			<section id="paid">
				<div class="inline-block">
					<div class="line-title"><span>Включить оплату</span></div>
					<div class="line-value">
						<input id="toggle-2" class="toggle" name="paid" type="checkbox">
						<label for="toggle-2"></label>
					</div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Секретный ключ</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="secretkey"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Логин</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="login"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Пароль</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="password"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Аккаунт</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="shoplogin"></div>
				</div>
			</section>
			<section id="mail">
				<div class="inline-block">
					<div class="line-title"><span>Системный E-mail адрес</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="system-email" value="admin@super-kadr32.ru" placeholder="admin@super-kadr32.ru"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Заголовок отправителя</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="system-email" value="Супер Кадр 32"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Метод отправки</span></div>
					<div class="line-value">
						<select class="selectbox" name="method">
							<option value="php">PHP Mail</option>
							<option value="smtp">SMTP</option>
						</select>
					</div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>SMTP хост</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="smtphost" value="ssl://smtp.yandex.ru"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>SMTP порт</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="smtpport" value="465"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>SMTP пользователь</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="smtplogin" value="admin@super-kadr32.ru"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>SMTP пароль</span></div>
					<div class="line-value"><input class="inputbox" type="password" name="smtppassword"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>E-mail отправителя</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="smtpfrom" value="admin@super-kadr32.ru"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Настройка SMTP HELO</span></div>
					<div class="line-value">
						<select class="selectbox" name="smtpmethod">
							<option value="helo">HELO</option>
							<option value="ehlo">EHLO</option>
						</select>
					</div>
				</div>
			</section>
			<section id="users">
				<div class="inline-block">
					<div class="line-title"><span>Включить авторизацию</span></div>
					<div class="line-value">
						<input id="toggle-3" class="toggle" name="auth" type="checkbox">
						<label for="toggle-3"></label>
					</div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Секретный ключ Facebook</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="secretkey-fb"></div>
				</div>
			</section>
			<section id="security">
				<div class="inline-block">
					<div class="line-title"><span>Включить reCAPTCHA</span></div>
					<div class="line-value">
						<input id="toggle-4" class="toggle" name="recaptcha" type="checkbox">
						<label for="toggle-4"></label>
					</div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Публичный ключ reCAPTCHA</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="publickey-recaptcha"></div>
				</div>
				<div class="inline-block">
					<div class="line-title"><span>Приватный ключ reCAPTCHA</span></div>
					<div class="line-value"><input class="inputbox" type="text" name="secretkey-recaptcha"></div>
				</div>
			</section>
		</div>
	</div>
</div>				
@endsection