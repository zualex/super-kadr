@extends('admin.app')

@section('content')


<h1>Пользователи</h1>
<a class="btn btn-small btn-success" href="{{ route('admin.users.create') }}">Добавить пользователя</a>
<br>
<br>

<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif

{!! HTML::ul($errors->all()) !!}


<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <td>ID</td>
            <td>Имя</td>
            <td>Email</td>
            <td>Уровень</td>
            <td width="220px">Действия</td>
        </tr>
    </thead>
    <tbody>
    @foreach($users as $key => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->name }}</td>
            <td >{{ $value->email }}</td>
            <td>{{ $value->level }}</td>

            <!-- we will also add show, edit, and delete buttons -->
            <td>

                <!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
                <a class="btn btn-small btn-info" href="{{ route('admin.users.edit', $value->id) }}">Изменить</a>
				
				
				{!! Form::open(array('route' => array('admin.users.destroy', $value->id), 'class' => 'btn')) !!}
                    {!! Form::hidden('_method', 'DELETE') !!}
                    {!! Form::submit('Удалить', array('class' => 'btn btn-warning')) !!}
                {!! Form::close() !!}
				
				
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection