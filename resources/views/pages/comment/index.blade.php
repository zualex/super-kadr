
@if(count($comments) > 0)
<div class="comments-list clear">
	@foreach($comments as $key => $value)
		<div class="comment-unit clear">
			<div class="avatar">
				@if($value->user->avatar)
						<a href=""><img src="{{ $value->user->avatar }}" alt=""></a>
				@else
						<a href=""><img src="{{ $defaultAvatar }}" alt=""></a>
				@endif
			</div>
			<div class="body">
				<div>
					<div class="info">
						<div class="author"><a href=""><span>{{ $value->user->name }}</span></a></div>
						<div class="time"><span>{{ $value->created_at }}</span></div>
					</div>
					<div class="message">
						<span>
						{!! $value->comment !!}
						</span>
					</div>
				</div>
			</div>
		</div>
	@endforeach
</div>
@endif