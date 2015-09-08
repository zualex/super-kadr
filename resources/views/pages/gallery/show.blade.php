@extends('app')

@section('content')

<div id="Container-Gallery" class="block-page">
	<div class="image">
		<img src="{{ $gallery->pathImages.'/o_'.$gallery->src  }}" alt="Описание изображения">
	</div>
	<div class="social">
		<div class="clear">
			<div class="details">
				<div class="description"><span>Оцените и прокомментируйте:</span></div>
				<div class="buttons">
					<div class="likes" onclick="likeGallery(this, {{ $gallery->id }}, '{{ route('gallery.like') }}')"><i class="fa pull-left fa-heart"></i><span>{{ count($gallery->likes) }}</span></div>
					<div class="comments"><i class="fa pull-left fa-comment"></i><span>{{ count($gallery->comments) }}</span></div>
				</div>
			</div>
			<div class="share">
				<div class="description"><span>или поделитесь с друзьями:</span></div>
				<div class="buttons">
					<a href="" class="icon social vk"><i class="fa fa-vk"></i></a>
					<a href="" class="icon social in"><i class="fa fa-instagram"></i></a>
					<a href="" class="icon social fb"><i class="fa fa-facebook"></i></a>
					<a href="" class="icon social tw"><i class="fa fa-twitter"></i></a>
					<a href="" class="icon social gg"><i class="fa fa-google-plus"></i></a>
				</div>
			</div>
		</div>
	</div>
	<div class="comments">
		<div class="header"><span>Комментарии:</span></div>
		
		
		<div class="wrap_comment"></div>
		<script>showComment('{{ route('comment.index', $gallery->id) }}')</script>
		
		
		<div class="add-comments">
			<div class="clear">
				<div class="avatar">
					@if(Auth::check() AND Auth::user()->avatar)
						<img src="{{ Auth::user()->avatar }}" alt="">
					@else
							<img src="{{ $defaultAvatar }}" alt="">
					@endif
				</div>
				<div class="body">
					<div class="textarea" id="1" contenteditable></div>
				</div>
				<div class="submit" onclick="setComment(this, {{ $gallery->id }}, '{{ route('comment.save') }}', '{{ route('comment.index', $gallery->id) }}')">
					<i class="fa fa-paper-plane"></i>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
