@extends('admin.app')

@section('content')
<h1>Редактирование {{ $user->name }}</h1>

<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all()) !!}

{!! Form::model($user, array('route' => array('admin.users.update', $user->id), 'method' => 'PUT')) !!}

    <div class="form-group">
        {!! Form::label('name', 'Имя') !!}
        {!! Form::text('name', null, array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('email', 'Email') !!}
        {!! Form::email('email', null, array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('level', 'Уровень') !!}
        {!! Form::select('level', array('user' => 'user', 'admin' => 'admin'), null, array('class' => 'form-control')) !!}
    </div>

    {!! Form::submit('Изменить', array('class' => 'btn btn-primary')) !!}

{!! Form::close() !!}

@endsection