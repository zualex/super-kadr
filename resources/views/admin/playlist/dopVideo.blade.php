
<form id="form-admin" role="form" method="POST" >
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
				
		
				
	@for($i=1;$i<=5;$i++)
		<div class="inline-block" style="border-bottom: 1px solid #CDCDCD;padding-bottom: 10px;">
			<div class="line-title"><span>Ролик {{ $i }}</span></div>
			<div class="line-value">
				<input class="inputbox" type="text" name="path{{ $i }}" value="{{ $data['extraVideo'][$i]['path'] }}" placeholder="Путь до ролика">
				<input style="margin-top:10px" class="inputbox" type="text" name="time{{ $i }}" value="{{ $data['extraVideo'][$i]['time'] }}" placeholder="Продолжительность">
			</div>
		</div>
	@endfor
	<div class="inline-block" style="border-bottom: 1px solid #CDCDCD;padding-bottom: 10px;">
			<div class="line-title"></div>
			<div class="line-value">
				<a href="#" class="btn add savesettings" onclick="actionAll('{{ route('admin.playlist.saveExtraVideo') }}')"><i class="fa pull-left fa-floppy-o"></i>Сохранить</a>
			</div>
	</div>
	
</form>

