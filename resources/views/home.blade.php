@extends('app')

@section('content')
<div id="slider" class="block color-1">
			<div class="header"><span>Самое популярное в галерее</span></div>
			<div class="body clear">
				<div class="slider" id="slider1">
					<div class="content">
						<div class="slide-list clear">
							<div class="slide" id="slide1">
								<a href="{{ route('gallery.show', 1) }}"><div class="image" style="background-image:url(/img/example/image501.jpg);"></div></a>
								<div class="info">
									<div class="likes"><i class="fa pull-left fa-heart"></i><span>11261</span></div>
									<div class="comments"><i class="fa pull-left fa-comment"></i><span>961</span></div>
								</div>
							</div>
							<div class="slide" id="slide2">
								<a href="{{ route('gallery.show', 1) }}"><div class="image" style="background-image:url(/img/example/image501.jpg);"></div></a>
								<div class="info">
									<div class="likes"><i class="fa pull-left fa-heart"></i><span>5960</span></div>
									<div class="comments"><i class="fa pull-left fa-comment"></i><span>110</span></div>
								</div>
							</div>
							<div class="slide" id="slide3">
								<a href="{{ route('gallery.show', 1) }}"><div class="image" style="background-image:url(/img/example/image501.jpg);"></div></a>
								<div class="info">
									<div class="likes"><i class="fa pull-left fa-heart"></i><span>1150</span></div>
									<div class="comments"><i class="fa pull-left fa-comment"></i><span>29</span></div>
								</div>
							</div>
							<div class="slide" id="slide4">
								<a href="{{ route('gallery.show', 1) }}"><div class="image" style="background-image:url(/img/example/image501.jpg);"></div></a>
								<div class="info">
									<div class="likes"><i class="fa pull-left fa-heart"></i><span>1150</span></div>
									<div class="comments"><i class="fa pull-left fa-comment"></i><span>29</span></div>
								</div>
							</div>
							<div class="slide" id="slide5">
								<a href="{{ route('gallery.show', 1) }}"><div class="image" style="background-image:url(/img/example/image501.jpg);"></div></a>
								<div class="info">
									<div class="likes"><i class="fa pull-left fa-heart"></i><span>1150</span></div>
									<div class="comments"><i class="fa pull-left fa-comment"></i><span>29</span></div>
								</div>
							</div>
							<div class="slide" id="slide6">
								<a href="{{ route('gallery.show', 1) }}"><div class="image" style="background-image:url(/img/example/image501.jpg);"></div></a>
								<div class="info">
									<div class="likes"><i class="fa pull-left fa-heart"></i><span>1150</span></div>
									<div class="comments"><i class="fa pull-left fa-comment"></i><span>29</span></div>
								</div>
							</div>
							<div class="slide" id="slide7">
								<a href="{{ route('gallery.show', 1) }}"><div class="image" style="background-image:url(/img/example/image501.jpg);"></div></a>
								<div class="info">
									<div class="likes"><i class="fa pull-left fa-heart"></i><span>1150</span></div>
									<div class="comments"><i class="fa pull-left fa-comment"></i><span>29</span></div>
								</div>
							</div>
						</div>
					</div>
					<div class="controls">
						<div class="nav-left"><i class="fa fa-chevron-left"></i></div>
						<div class="nav-right"><i class="fa fa-chevron-right"></i></div>
					</div>
				</div>
			</div>
		</div>
		<div id="howwork" class="block color-1">
			<div class="header"><span>Как это работает?</span></div>
			<div class="body clear">
				<div class="step">
					<div class="image"><div style="background-image:url(/img/step1.png);"></div></div>
					<div class="title"><span>Сфотографируй</span></div>
				</div>
				<div class="step">
					<div class="image"><div style="background-image:url(/img/step2.png);"></div></div>
					<div class="title"><span>Загрузи свое изображение</span></div>
				</div>
				<div class="step">
					<div class="image"><div style="background-image:url(/img/step3.png);"></div></div>
					<div class="title"><span>Выбери время показа и оплати</span></div>
				</div>
				<div class="step">
					<div class="image"><div style="background-image:url(/img/step4.png);"></div></div>
					<div class="title"><span>Твое фото появится на экранах города!</span></div>
				</div>
			</div>
		</div>
		<div id="try" class="block color-2">
			<div class="header"><span>Разместите и вы свое фото!</span></div>
			<div class="body clear">
				<div class="text">
					<span>Выберите файл изображения на своем устройстве и оптимизируйте его размер для отображения на экране при помощи формы ниже.</span>
				</div>
			</div>
		</div>
		<div id="upload" class="block">
			<div class="body clear">
				<style>
					@foreach($data['paramMonitor'] as $key => $value)
						.activeMonitor_{{ $key }}{
							width:  {{ $value['siteWidth']+40 }}px;
							height: {{ $value['siteHeight']+40 }}px;	
						}
						.activeMonitor_{{ $key }} #croppic{
							width:  {{ $value['siteWidth'] }}px;
							height: {{ $value['siteHeight'] }}px;	
						}
					@endforeach	
				</style>
				
				<div id="monitor-change-class" class="monitor activeMonitor_1">
					<div id="croppic"></div>
					<div id="upload-btn"><div><i class="fa pull-left fa-camera"></i><span>Загрузи фото</span></div></div>
				</div>
				<div class="stand"></div>
				<script type="text/javascript">
					var croppicHeaderOptions = {
							cropUrl:'/croppic/img_crop_to_file.php',
							cropData:{
								'dataInfo' : '{{ $data['sessionUpload'] }}',
								'monitor' : 0,	//change in script.js
								@foreach($data['paramMonitor'] as $key => $value)
									"cropOrigW_{{ $key }}" : {{ $value['origWidth'] }},
									"cropOrigH_{{ $key }}" : {{ $value['origHeight'] }},
								@endforeach	
							},
							customUploadButtonId:'upload-btn',
							modal:false,
							processInline:true,
							loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
							onBeforeImgUpload: function(){ console.log('onBeforeImgUpload') },
							onAfterImgUpload: function(){ console.log('onAfterImgUpload') },
							onImgDrag: function(){ console.log('onImgDrag') },
							onImgZoom: function(){ console.log('onImgZoom') },
							onBeforeImgCrop: function(){ console.log('onBeforeImgCrop') },
							onAfterImgCrop:function(){ console.log('onAfterImgCrop') },
							onError:function(errormessage){ alert(errormessage) }
					}	
					var croppic = new Croppic('croppic', croppicHeaderOptions);
				</script>
			</div>
		</div>
		<div id="schedule" class="block color-1">
			<div class="body clear">
				<div class="tariff" data-tarif = "" data-monitor = "" data-dateShow = "">
					<div class="header"><span>тариф</span></div>
					<div class="switch">
					
						@foreach($data['tarifs'] as $key => $value)
							<input type="hidden" name="tarif_id" value="{{ $value->name }}">
							<div class="label item_{{ $value->id }} hidden" data-tarif = "{{ $value->id }}"><span>{{ $value->name }}</span></div>
						@endforeach
						
							<div class="controls">
								<div class="nav-left"><i class="fa fa-chevron-left"></i></div>
								<div class="nav-right"><i class="fa fa-chevron-right"></i></div>
							</div>
					</div>
					@foreach($data['tarifs'] as $key => $value)
						<div class="info info_{{ $value->id }} hidden">
							<div class="views-num"><span>{{ $value->desc_main }}</span></div>
							<div class="duration"><span>{{ $value->desc_dop }}</span></div>
							<div class="price"><span>{{ $value->price }}<i class="fa pull-right fa-rub"></i></span></div>
						</div>
					@endforeach
					
					<div class="monitor">
						@foreach($data['paramMonitor'] as $key => $value)
							<input type="hidden" id="monitorWidth_{{ $key }}" name="monitorWidth_{{ $key }}" value="{{ $value['siteWidth'] }}">
							<input type="hidden" id="monitorHeight_{{ $key }}" name="monitorHeight_{{ $key }}" value="{{ $value['siteHeight'] }}">
						@endforeach	
						<select name="monitor">
							@foreach($data['paramMonitor'] as $key => $value)
								 <option value="{{ $key }}">Экран {{ $key }}</option>
							@endforeach	
						</select>
					</div>
					<div class="pay">
						<a href="{{ route('gallery.create') }}" title="Оплатить заказ"><i class="fa pull-left fa-credit-card"></i><span>Оплатить</a>
					</div>
				</div>
				<div class="schedule-block">
					<div class="content">
						<div class="header"><span>Выберите дату и время начала показа</span></div>
						<div class="time-list">
							<div class="dates">
								<span class="item" ></span>
							</div>
							<div class="times tab-block">
								{!! $data['dateContent'] !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="social" class="block color-3">
			<div class="header"><span>Мы в социальных сетях</span></div>
			<div class="body clear">
				<div class="col-3">
					<script type="text/javascript" src="//vk.com/js/api/openapi.js?116"></script>
					<div id="vk_groups1"></div>
					<script type="text/javascript">
					VK.Widgets.Group("vk_groups1", {mode: 0, width: "auto", height: "350", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 43126172);
					</script>
				</div>
				<div class="col-3">
					<div id="vk_groups2"></div>
					<script type="text/javascript">
					VK.Widgets.Group("vk_groups2", {mode: 0, width: "auto", height: "350", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 43126172);

					</script>
				</div>
				<div class="col-3">
					<div id="vk_groups3"></div>
					<script type="text/javascript">
					VK.Widgets.Group("vk_groups3", {mode: 0, width: "auto", height: "350", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 43126172);

					</script>
				</div>
			</div>
		</div>
		<div id="partners" class="block color-2">
				<div class="header"><span>С нами сотрудничают</span></div>
				<div class="body clear">
				
				</div>
		</div>
		<div id="contacts" class="block color-3">
			<div class="body">
				<div class="logo">
					<a href="{{ route('main') }}" title="Супер Кадр"><img src="/img/logo.png" alt=""></a>
				</div>
				<div class="phone"><span>8 (555) 555-55-55</span></div>
				<div class="info"><span>По всем вопросам, связанным с работой сайта обращайтесь по телефону.</span></div>
			</div>
		</div>
@endsection
