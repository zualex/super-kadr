@extends('admin.app')

@section('content')
<h1>Добавление пользователя</h1>
<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all()) !!}

<div class="row">
	<div class="col-md-8">
{!! Form::open(array('url' => 'admin/users')) !!}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="form-group">
        {!! Form::label('name', 'Имя') !!}
        {!!Form::text('name', Input::old('name'), array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('email', 'Email') !!}
        {!! Form::email('email', Input::old('email'), array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('level', 'Уровень') !!}
        {!! Form::select('level', array('user' => 'user', 'admin' => 'admin'), Input::old('level'), array('class' => 'form-control')) !!}
    </div>
	
	<div class="form-group">
        {!! Form::label('password', 'Пароль') !!}
        {!! Form::password('password', array('class' => 'form-control')) !!}
    </div>
	
	<div class="form-group">
        {!! Form::label('password_confirmation', 'Еще раз пароль') !!}
        {!! Form::password('password_confirmation', array('class' => 'form-control')) !!}
    </div>

    {!! Form::submit('Создать пользователя', array('class' => 'btn btn-primary')) !!}

{!! Form::close() !!}
	</div>
</div>
@endsection
