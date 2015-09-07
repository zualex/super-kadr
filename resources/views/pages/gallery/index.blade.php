@extends('app')

@section('content')
<div id="Container-Gallery" class="block-page">
	<div class="content clear">
		@foreach($data['gallery'] as $key => $value)
			<div class="item">
				<a href="{{ route('gallery.show', $value->id) }}"><div class="image" style="background-image:url('{{ $data['pathImages'].'/m_'.$value->src }}');"></div></a>
				<div class="info clear">
					<div class="likes"><i class="fa pull-left fa-heart"></i><span>{{ $value->like_count }}</span></div>
					<div class="comments"><i class="fa pull-left fa-comment"></i><span>{{ $value->comment_count }}</span></div>
				</div>
			</div>
		@endforeach
	</div>
	<div id="navigation" class="clear">
		<div class="nav prev"><a href="#"><span><i class="fa fa-chevron-left"></i></span></a></div>
		<div class="pages">
			<ul>
				<li id="page1" class="active"><a href="" title=""><span>1</span></a></li>
				<li id="divider"><span>...</span></li>
				<li id="page2"><a href="" title=""><span>10</span></a></li>
				<li id="page3"><a href="" title=""><span>11</span></a></li>
				<li id="page4"><a href="" title=""><span>12</span></a></li>
				<li id="divider"><span>...</span></li>
				<li id="page6"><a href="" title=""><span>20</span></a></li>
			</ul>
		</div>
		<div class="nav next"><a href="#"><span><i class="fa fa-chevron-right"></i></span></a></div>
	</div>
</div>
@endsection
