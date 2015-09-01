@extends('admin.app')

@section('content')
<h1>Административная панель</h1>

@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
@endsection