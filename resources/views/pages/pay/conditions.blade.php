@extends('app')

@section('content')
<div class="wrapper">
	<div class="block">
		<div class="header"><span>Условия</span></div>
	
	
		<div class="body clear">
			<div class="text">
				<span>Здесь условия договора</span>
			</div>

			
			<form action='{{ route('pay.index', $gallery_id) }}' method="GET" onsubmit="if($('#yes_form').prop('checked')){return true;}else{alert('необходимо принять условия договора')}return false;">
				<label for="yes_form">
					<input type="checkbox" name="yes_form" id="yes_form" value="1">
					принять условия договора
				</label>
				<br>
				<br>
				<input type="submit" value='Оплатить'>
			</form>
			
		</div>
	
	</div>
</div>
@endsection