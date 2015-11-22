@extends('app')

@section('content')
<div class="wrapper">
	<div class="block">
		<div class="header"><span>{{ $data['user']->name }}</span></div>
	</div>
</div>

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
</div>
@endsection
