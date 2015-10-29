
<form id="form-admin" role="form" method="POST" >
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
				
		
	<?php $count = 0 ?>			
	@foreach($data['extraVideo'] as $i => $item)
		<?php $count++ ?>	
		@if($count > 5)
			<div style="position:relative;">
			<a href="{{ route('admin.playlist.deleteExtraVideo', $i) }}" class="btn del" style="position: absolute;left: 540px;">
				<i class="fa pull-left fa-trash"></i>Удалить</a>
			</div>
		@endif

		
		<div class="inline-block" style="border-bottom: 1px solid #CDCDCD;padding-bottom: 10px;">
			<div class="line-title"><span>Ролик {{ $i }}</span></div>
			<div class="line-value">
				<input class="inputbox" type="text" name="path[{{ $i }}]" value="{{ $item['path'] }}" placeholder="Путь до ролика">
				<input style="margin-top:10px" class="inputbox" type="text" name="time[{{ $i }}]" value="{{ $item['time'] }}" placeholder="Продолжительность">
			</div>
		</div>
	@endforeach
	<div class="insertField"></div>
	
	
	<div class="inline-block" style="border-bottom: 1px solid #CDCDCD;padding-bottom: 10px;">
			<div class="line-title"></div>
			<div class="line-value">
				<a href="#" class="btn add savesettings" onclick="actionAll('{{ route('admin.playlist.saveExtraVideo') }}')"><i class="fa pull-left fa-floppy-o"></i>Сохранить</a>
			</div>
	</div>
	
	<div class="inline-block">
           <div class="line-value">
               <div class="btn-group">
                   <a href="#" class="btn add addvars" onclick="$('.insertField').append($('.copyField').html())"><i class="fa pull-left fa-plus-square-o"></i>Добавить</a>
               </div>
           </div>
   </div>
	
</form>



<div class="copyField" style="display:none">
	<div class="inline-block" style="border-bottom: 1px solid #CDCDCD;padding-bottom: 10px;    margin-bottom: 10px;">
		<div style="position:relative;">
			<a href="#" class="btn del" onclick="$(this).parent().parent().remove();return false;" style="position: absolute;left: 540px;">
				<i class="fa pull-left fa-trash"></i>Удалить
			</a>
		</div>
	
		<div class="line-title"><span>Новый ролик</span></div>
		<div class="line-value">
			<input class="inputbox" type="text" name="path[]" value="" placeholder="Путь до ролика">
			<input style="margin-top:10px" class="inputbox" type="text" name="time[]" value="0" placeholder="Продолжительность">
		</div>
	</div>
</div>
