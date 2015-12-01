@extends('app')

@section('content')
<div class="wrapper">
	<div class="block">
		<div class="header"><span>Просмотр трансляции 1</span></div>
		<div class="body clear">
			<div class="text">
				<video src="rtsp://admin:XehTXhyE@172.30.95.2:554/h264">
					Your browser does not support the VIDEO tag and/or RTP streams.
				</video>
			</div>
		</div>
	</div>
</div>
@endsection