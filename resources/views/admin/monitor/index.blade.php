@extends('admin.app')

@section('content')
<div id="orders">
	<div class="header">
		<div class="clear">
			<div class="title"><span>Экраны</span></div>
			<div class="controls">
				<div class="btn-group">
					<a href="#" class="btn add" onclick="actionAll('{{ route('admin.monitor.success') }}')"><i class="fa pull-left fa-floppy-o"></i>Сохранить</a>
				</div>
			</div>
		</div>
	</div>

	
	
	<div class=" tab-block">
		<div class="orders tabs">
			<ul>
				<li class="active">Экран 1</li>
				<li>Экран 2</li>
			</ul>
		</div>
		
		@if (Session::has('message'))
			<br>
			<div class="alert alert-info">{{ Session::get('message') }}</div>
		@endif
		
		<form id="form-admin" role="form" method="POST" >
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		
		<div class="box-content">
			<section class="visible">
				@if(count($data['monitor1']) > 0)
					<div class="inline-block">
						<div class="line-title"><span>Ширина Croppic</span></div>
						<input class="inputbox" type="text" name="siteWidth1" value="{{ $data['monitor1']->siteWidth }}" placeholder="Ширина Croppic"> px
					</div>
					<div class="inline-block">
						<div class="line-title"><span>Высота Croppic</span></div>
						<input class="inputbox" type="text" name="siteHeight1" value="{{ $data['monitor1']->siteHeight }}" placeholder="Высота Croppic"> px
					</div>
					
					<div class="inline-block">
						<div class="line-title"><span>Ширина большого изображения</span></div>
						<input class="inputbox" type="text" name="origWidth1" value="{{ $data['monitor1']->origWidth }}" placeholder="Ширина большого изображения"> px
					</div>
					<div class="inline-block">
						<div class="line-title"><span>Высота большого изображения</span></div>
						<input class="inputbox" type="text" name="origHeight1" value="{{ $data['monitor1']->origHeight }}" placeholder="Высота большого изображения"> px
					</div>
					
					<div class="inline-block">
						<div class="line-title"><span>Ширина среднего изображения</span></div>
						<input class="inputbox" type="text" name="mediumWidth1" value="{{ $data['monitor1']->mediumWidth }}" placeholder="Ширина среднего изображения"> px
					</div>
					<div class="inline-block">
						<div class="line-title"><span>Высота среднего изображения</span></div>
						<input class="inputbox" type="text" name="mediumHeight1" value="{{ $data['monitor1']->mediumHeight }}" placeholder="Высота среднего изображения"> px
					</div>
					
					<div class="inline-block">
						<div class="line-title"><span>Ширина маленького изображения</span></div>
						<input class="inputbox" type="text" name="smallWidth1" value="{{ $data['monitor1']->smallWidth }}" placeholder="Ширина маленького изображения"> px
					</div>
					<div class="inline-block">
						<div class="line-title"><span>Высота маленького изображения</span></div>
						<input class="inputbox" type="text" name="smallHeight1" value="{{ $data['monitor1']->smallHeight }}" placeholder="Высота маленького изображения"> px
					</div>
					
					
				@endif
			</section>
			<section>
				@if(count($data['monitor2']) > 0)
					
					<div class="inline-block">
						<div class="line-title"><span>Ширина Croppic</span></div>
						<input class="inputbox" type="text" name="siteWidth2" value="{{ $data['monitor2']->siteWidth }}" placeholder="Ширина Croppic"> px
					</div>
					<div class="inline-block">
						<div class="line-title"><span>Высота Croppic</span></div>
						<input class="inputbox" type="text" name="siteHeight2" value="{{ $data['monitor2']->siteHeight }}" placeholder="Высота Croppic"> px
					</div>
					
					<div class="inline-block">
						<div class="line-title"><span>Ширина большого изображения</span></div>
						<input class="inputbox" type="text" name="origWidth2" value="{{ $data['monitor2']->origWidth }}" placeholder="Ширина большого изображения"> px
					</div>
					<div class="inline-block">
						<div class="line-title"><span>Высота большого изображения</span></div>
						<input class="inputbox" type="text" name="origHeight2" value="{{ $data['monitor2']->origHeight }}" placeholder="Высота большого изображения"> px
					</div>
					
					<div class="inline-block">
						<div class="line-title"><span>Ширина среднего изображения</span></div>
						<input class="inputbox" type="text" name="mediumWidth2" value="{{ $data['monitor2']->mediumWidth }}" placeholder="Ширина среднего изображения"> px
					</div>
					<div class="inline-block">
						<div class="line-title"><span>Высота среднего изображения</span></div>
						<input class="inputbox" type="text" name="mediumHeight2" value="{{ $data['monitor2']->mediumHeight }}" placeholder="Высота среднего изображения"> px
					</div>
					
					<div class="inline-block">
						<div class="line-title"><span>Ширина маленького изображения</span></div>
						<input class="inputbox" type="text" name="smallWidth2" value="{{ $data['monitor2']->smallWidth }}" placeholder="Ширина маленького изображения"> px
					</div>
					<div class="inline-block">
						<div class="line-title"><span>Высота маленького изображения</span></div>
						<input class="inputbox" type="text" name="smallHeight2" value="{{ $data['monitor2']->smallHeight }}" placeholder="Высота маленького изображения"> px
					</div>
				
				
				@endif
			</section>
			
		</div>
		</form>
	</div>
</div>
@endsection