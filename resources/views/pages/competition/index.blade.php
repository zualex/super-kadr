@extends('app')

@section('content')
<div class="wrapper">
	<div class="block">
		<div class="header"><span>Конкурс: {{ $data['name'] }}</span></div>
		<div class="body clear">
			<p>
				<b>Сроки проведения:</b>
					c {{ $data['date_start'] }} 
					@if($data['date_start'] != $data['date_end'])
						по {{ $data['date_end'] }} 
					@endif
			</p>
			<p>
				{!! $data['text'] !!}
			</p>
		</div>

		
		<div id="slider" class="block color-1">
			<div class="header"><span>ТОП-10</span></div>
			<div class="body clear">
				<div class="slider" id="slider1">
					<div class="content">
						<div class="slide-list clear">
							@foreach($data['top'] as $key => $value)
								<div class="slide">
									<a href="{{ route('gallery.show', $value->id) }}"><div class="image" style="background-image:url('{{ $data['pathImages'].'/s_'.$value->src }}');"></div></a>
									<div class="info">
										<div class="likes" onclick="likeGallery(this, {{ $value->id }}, '{{ route('gallery.like') }}')"><i class="fa pull-left fa-heart"></i><span>{{ $value->like_count }}</span></div>
										<div class="comments" onclick="window.location.href='{{ route('gallery.show', $value->id) }}#comment'"><i class="fa pull-left fa-comment"></i><span>{{ $value->comment_count }}</span></div>
									</div>
								</div>
							@endforeach	
						</div>
					</div>
					<div class="controls">
						<div class="nav-left"><i class="fa fa-chevron-left"></i></div>
						<div class="nav-right"><i class="fa fa-chevron-right"></i></div>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		
		<div id="slider" class="block color-1">
			<div class="header"><span>Лучший автор</span></div>
			<div class="body clear">
				<div class="slider" id="slider1">
					<div class="content">
						<div class="slide-list clear">
							@foreach($data['autor'] as $key => $value)
								<div class="slide">
									<a href="{{ route('competition.show', $value['user_id']) }}">
										<div class="image" style="
											@if($value['avatar'])
												background-image:url('{{ $value['avatar'] }}')
											@else
												background-image:url('{{ $defaultAvatar }}')
											@endif
										"></div>
									</a>
									<div class="info">
										<div class="likes">
											<i class="fa pull-left fa-heart"></i><span>{{ $value['count']}}</span>
										</div>
									</div>
								</div>
							@endforeach	
						</div>
					</div>
					<div class="controls">
						<div class="nav-left"><i class="fa fa-chevron-left"></i></div>
						<div class="nav-right"><i class="fa fa-chevron-right"></i></div>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="header"><span>Конкурсые работы</span></div>
		<div id="Container-Gallery" class="block-page">
			<div class="content clear">
				@foreach($data['gallery'] as $key => $value)
					<div class="item">
						<a href="{{ route('gallery.show', $value->id) }}"><div class="image" style="background-image:url('{{ $data['pathImages'].'/m_'.$value->src }}');"></div></a>
						<div class="info clear">
							<div class="likes" onclick="likeGallery(this, {{ $value->id }}, '{{ route('gallery.like') }}')">
								<i class="fa pull-left fa-heart"></i>
								<span>
										{{ $value->like_count }}
								</span>
							</div>
							<div class="comments" onclick="window.location.href='{{ route('gallery.show', $value->id) }}#comment'"><i class="fa pull-left fa-comment"></i><span>{{ $value->comment_count }}</span></div>
						</div>
					</div>
				@endforeach
			</div>
			
			@if($data['gallery']->lastPage() > 1)
			<div id="navigation" class="clear">
				<div class="nav prev"><a href="{{ $data['gallery']->url($data['gallery']->currentPage() - 1) }}"><span><i class="fa fa-chevron-left"></i></span></a></div>
				<div class="pages">
					<ul>
						@for ($i = 1; $data['gallery']->lastPage() >= $i; $i++)
							@if($i == $data['gallery']->currentPage())
								<li class="active"><a href="{{ $data['gallery']->url($i) }}" ><span>{{ $i }}</span></a></li>
							@else
								
								@if($i == 1)
									<li><a href="{{ $data['gallery']->url($i) }}" ><span>{{ $i }}</span></a></li>
								@elseif($i == $data['gallery']->lastPage())
									<li><a href="{{ $data['gallery']->url($i) }}" ><span>{{ $i }}</span></a></li>
								@elseif($data['gallery']->currentPage() - $i > 2)
								
								@elseif($data['gallery']->currentPage() - $i > 1)
									<li><span>...</span></li>		
								@elseif($data['gallery']->currentPage() - $i < -2)
									
								@elseif($data['gallery']->currentPage() - $i < -1)
									<li><span>...</span></li>								
								@else
									<li><a href="{{ $data['gallery']->url($i) }}" ><span>{{ $i }}</span></a></li>
								@endif
							

								
							@endif
						@endfor
					</ul>
				</div>
				<div class="nav next"><a href="{{ $data['gallery']->nextPageUrl() }}"><span><i class="fa fa-chevron-right"></i></span></a></div>
			</div>
			@endif
			
			
		</div>
		
		
		
		
		
			
			

	</div>
</div>
@endsection
