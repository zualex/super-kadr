@extends('admin.app')

@section('content')


<h1>Администраторы</h1>

<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <td>ID</td>
            <td>Имя</td>
            <td>Email</td>
            <td>Уровень</td>
            <td>Действия</td>
        </tr>
    </thead>
    <tbody>
    @foreach($users as $key => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->name }}</td>
            <td>{{ $value->email }}</td>
            <td>{{ $value->level }}</td>

            <!-- we will also add show, edit, and delete buttons -->
            <td>

                <!-- delete the nerd (uses the destroy method DESTROY /nerds/{id} -->
                <!-- we will add this later since its a little more complicated than the other two buttons -->
                {!! Form::open(array('url' => 'nerds/' . $value->id, 'class' => 'pull-right')) !!}
                    {!! Form::hidden('_method', 'DELETE') !!}
                    {!! Form::submit('Delete this Nerd', array('class' => 'btn btn-warning')) !!}
                {!! Form::close() !!}

				
                <!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
                <a class="btn btn-small btn-info" href="{{ URL::to('nerds/' . $value->id . '/edit') }}">Edit this Nerd</a>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection