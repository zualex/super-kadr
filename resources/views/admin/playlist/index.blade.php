@extends('admin.app')

@section('content')
<div id="orders">
<div class="header">
	<div class="clear">
		<div class="title"><span>Плейлисты</span></div>
	</div>
</div>



<div class="content-orders tab-block">
	<div class="orders tabs">
		<ul>
			<li class="active">Экран 1</li>
			<li>Экран 2</li>
			<li>Добавочные ролики</li>
		</ul>
	</div>
		
	@if (Session::has('message'))
		<br>
		<div class="alert alert-info">{{ Session::get('message') }}</div>
	@endif


		
		
<div class="playlists tab-block">
	<section  class="visible">
		@include('admin.playlist.showInit1')
		@include('admin.playlist.showGallery1')
	</section>
	
	
	<section >
		@include('admin.playlist.showInit2')
		@include('admin.playlist.showGallery2')
	</section>

	
	<section style="padding: 10px 20px">
		@include('admin.playlist.dopVideo')
	</section>
	
</div>
</div>
</div>			
@endsection