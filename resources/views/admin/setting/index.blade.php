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
							
							<div class="line-value">
							@if($value->type_input == 'checkbox')
								@if($value->value == 1)
									<input id="{{ $value->name }}" class="toggle" name="{{ $value->name }}" type="checkbox" value="1" checked>
								@else
									<input id="{{ $value->name }}" class="toggle" name="{{ $value->name }}" type="checkbox" value="1">
								@endif
								<label for="{{ $value->name }}"></label>
							@else
								<input class="inputbox" type="text" name="{{ $value->name }}" value="{{ $value->value }}" placeholder="Название {{ $value->value }}">
							@endif
							</div>
							
						</div>
					@endforeach
				@endif

			</section>
			<section id="paid">
				
				@if(count($data['settingPay']) > 0)
					@foreach($data['settingPay'] as $key => $value)
						<div class="inline-block">
							<div class="line-title"><span>{{ $value->caption }}</span></div>
							
							<div class="line-value">
							@if($value->type_input == 'checkbox')
								@if($value->value == 1)
									<input id="{{ $value->name }}" class="toggle" name="{{ $value->name }}" type="checkbox" value="1" checked>
								@else
									<input id="{{ $value->name }}" class="toggle" name="{{ $value->name }}" type="checkbox" value="1">
								@endif
								<label for="{{ $value->name }}"></label>
							@else
								<input class="inputbox" type="text" name="{{ $value->name }}" value="{{ $value->value }}" placeholder="Название {{ $value->value }}">
							@endif
							</div>
							
						</div>
					@endforeach
				@endif
				
			</section>
			<section id="users">

				@if(count($data['settingUser']) > 0)
					@foreach($data['settingUser'] as $key => $value)
						<div class="inline-block">
							<div class="line-title"><span>{{ $value->caption }}</span></div>
							
							<div class="line-value">
							@if($value->type_input == 'checkbox')
								@if($value->value == 1)
									<input id="{{ $value->name }}" class="toggle" name="{{ $value->name }}" type="checkbox" value="1" checked>
								@else
									<input id="{{ $value->name }}" class="toggle" name="{{ $value->name }}" type="checkbox" value="1">
								@endif
								<label for="{{ $value->name }}"></label>
							@else
								<input class="inputbox" type="text" name="{{ $value->name }}" value="{{ $value->value }}" placeholder="Название {{ $value->value }}">
							@endif
							</div>
							
						</div>
					@endforeach
				@endif
			
			</section>
		</div>
	</div>
</div>				
@endsection