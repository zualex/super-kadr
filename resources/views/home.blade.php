@extends('app')

@section('content')
		<div id="slider" class="block color-1">
			<div class="header"><span>Самое популярное в галерее</span></div>
			<div class="body clear">
				<div class="slider" id="slider1">
					<div class="content">
						<div class="slide-list clear">
							@foreach($data['mainGallery']['galleries'] as $key => $value)
								<div class="slide">
									<a href="{{ route('gallery.show', $value->id) }}"><div class="image" style="background-image:url('{{ $data['mainGallery']['pathImages'].'/s_'.$value->src }}');"></div></a>
									<div class="info">
										<div class="likes" onclick="likeGallery(this, {{ $value->id }}, '{{ route('gallery.like') }}')"><i class="fa pull-left fa-heart"></i><span>{{ $value->like_count }}</span></div>
										<div class="comments" onclick="window.location.href='{{ route('gallery.show', $value->id) }}#comment'"><i class="fa pull-left fa-comment"></i><span>{{ $value->comment_count }}</span></div>
									</div>
								</div>
							@endforeach	
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
			<div class="header"><span>1. Выберите экран</span></div>
		</div>
		<div id="upload" class="block">
			<div class="body clear">			
				
				<div class="monitor-select clear">						
					@foreach($data['paramMonitor'] as $key => $value)
						 <div class="select" data-monitor="{{ $value['id'] }}" data-width="{{ $value['siteWidth'] }}" data-height="{{ $value['siteHeight'] }}"><span>Экран {{ $value['number'] }}</span></div>
					@endforeach
				</div>
				
				<div id="monitor-change-class" class="monitor">
					<div id="croppic"></div>
					<div id="upload-btn">
						<div class="upload"><i class="fa pull-left fa-camera"></i><span>Загрузи фото</span></div>
						<div class="description"><span>Выберите картинку или фото на своем устройстве и оптимизируйте их размер для корректного отображения на экране.</span></div>
					</div>
				</div>
				<div class="stand"></div>
				
				<script type="text/javascript">
					var croppicHeaderOptions = {
							cropUrl:'{{ route('gallery.upload') }}',
							cropData:{
								'monitor' : 0,	//change in script.js
							},
							customUploadButtonId:'upload-btn',
							modal:false,
							processInline:true,
							loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
							onBeforeImgUpload: function(){  },
							onAfterImgUpload: function(){ },
							onImgDrag: function(){  },
							onImgZoom: function(){  },
							onBeforeImgCrop: function(){  },
							onAfterImgCrop:function(){
								$('.tariffs').attr('data-image', response.url);
							},
							onError:function(errormessage){ alert(errormessage) }
					}	
					var croppic = new Croppic('croppic', croppicHeaderOptions);
				</script>
			</div>
		</div>
		<div id="tariffs" class="block color-1">
			<div class="header"><span>2. Выберите тариф</span></div>
			<div class="body clear">
				<div class="tariffs" data-url = "{{ route('gallery.create') }}" data-tariff = "" data-monitor = "" data-dateShow = "" data-image = "">
					@foreach($data['tarifs'] as $key => $value)
					<div class="tariff">
						<div class="header">
							<span>тариф</span>
							<input type="hidden" name="tarif_id" value="{{ $value->name }}">
							<span class="title">{{ $value->name }}</span>
						</div>
						<div class="info info_{{ $value->id }}">
							<div class="views-num"><span>{{ $value->desc_main }}</span></div>
							<div class="duration"><span>{{ $value->desc_dop }}</span></div>
							<div class="price"><span>{{ $value->price }}<i class="fa pull-right fa-rub"></i></span></div>
						</div>
						<div class="button">
							<div class="select" tariff-id="{{ $value->id }}"><span>Выбрать</span></div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
		<div id="schedule" class="block color-1">
			<div class="header"><span>3. Выберите дату и время начала показа</span></div>
			<div class="body clear">
				<div class="schedule-block">
					<div class="content">
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
				<div class="pay">
					<a href="#" title="Оплатить заказ" onclick="paySite(this);return false;"><i class="fa pull-left fa-credit-card"></i><span>Оплатить</a>
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
				<div class="phone"><span>8 (910) 333-37-48</span></div>
				<div class="info"><span>По всем вопросам, связанным с работой сайта обращайтесь по телефону.</span></div>
			</div>
		</div>
@endsection
