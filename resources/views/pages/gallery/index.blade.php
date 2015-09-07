@extends('app')

@section('content')
<div id="Container-Gallery" class="block-page">
	<div class="content clear">
		@foreach($data['gallery'] as $key => $value)
			<div class="item">
				<a href="{{ route('gallery.show', $value->id) }}"><div class="image" style="background-image:url('{{ $data['pathImages'].'/m_'.$value->src }}');"></div></a>
				<div class="info clear">
					<div class="likes" onclick="likeGallery(this, {{ $value->id }}, '{{ route('gallery.like') }}')"><i class="fa pull-left fa-heart"></i><span>{{ $value->like_count }}</span></div>
					<div class="comments"><i class="fa pull-left fa-comment"></i><span>{{ $value->comment_count }}</span></div>
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
@endsection
