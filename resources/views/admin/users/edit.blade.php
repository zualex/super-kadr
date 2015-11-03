@extends('admin.app')

@section('content')
<div>
	<div class="header">
		<div class="clear">
			<div class="title"><span>Редактирование {{ $user->name }}</span></div>
		</div>
	</div>

	@if (Session::has('message'))
		<br>
		<div class="alert alert-info">{{ Session::get('message') }}</div>
	@endif
	
	@if(count($errors) > 0)
		<div class="alert alert-info">{!! HTML::ul($errors->all()) !!}</div>
	@endif
	
	
	<div class="site-settings tab-block" style="padding: 10px 20px;">
		
		<form id="form-admin" role="form" method="POST" action="{{ route('admin.users.update', $user->id) }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="inline-block">
				<div class="line-title"><span>Имя</span></div>
				<div class="line-value">
					<input class="inputbox" type="text" name="name" value="{{ $user->name }}" placeholder="{{ $user->name }}">
				</div>
			</div>
			
			<div class="inline-block">
				<div class="line-title"><span>Email</span></div>
				<div class="line-value">
					<input class="inputbox" type="text" name="email" value="{{ $user->email }}" placeholder="{{ $user->email }}">
				</div>
			</div>
			
			<div class="inline-block">
				<div class="line-title"><span>Роль</span></div>
				<div class="line-value">
					<select name="level">
						<option value="moderator" @if($user->level == 'moderator') selected @endif>Модератор</option>
						<option value="admin" @if($user->level == 'admin') selected @endif>Администратор</option>
					</select>
				</div>
			</div>
			
			<div class="inline-block">
				<div class="line-title">&nbsp;</div>
				<div class="line-value">
					<input type="submit" class="btn add savesettings" value="Сохранить">
				</div>
			</div>
		</form>
		
	</div>
</div>
@endsection