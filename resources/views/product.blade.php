@extends('layouts.main', [
	"title"			=> $title,
	"meta_keywords"	=> $meta_keywords,
	"meta_desc"		=> $meta_desc
])

@section('content')
	<div id="product-info">
		<div class="as-tbl-100">
			@if ( !empty($product->images) && is_array($product->images) && sizeof($product->images) )
				<div class="pull-left">
					@if (!empty($product->images[0]))
						<img id="cover-main" width="407" height="540"
							 title="{{ $product->name }} - cover 1"
							 src="/images/md_{{ $product->images[0] }}" original="{{ $product->images[0] }}" />
					@endif

					@if (sizeof($product->images) > 1)
						<div>
							@foreach ($product->images as $key => $val)
								@continue($key === 0)		   
								<img class="cover-thumb" width="75" height="100" 
									 title="{{ $product->name }} - cover {{ $key+2 }}"
									 src="/images/sm_{{ $val }}" original="{{ $val }}" />
							@endforeach
						</div>
					@endif
				</div>
			@else
				<div class="pull-left">&nbsp;</div>
			@endif
				
			<div class="pull-left">
				<div id="like-wrapper">
					<table cellspacing="0" cellpadding="0">
						<tr>
							@php
								$has_like_up = $has_like_down = "";
								$searched = session()->has('user_id')? 
												$stat['likes']->where('user_id', session('user_id'))->first():												$stat['likes']->where('ses_id', session()->getId())->first();
								
								if(is_null($searched) === false){
									$searched->is_up? $has_like_up = "active": $has_like_down = "active";
								}
							@endphp
							<td align="left" valign="middle">
								<i class="fa fa-thumbs-up like-btn {{ $has_like_up }}" 
								   onclick="toggleLike(this, {{ $product->id }}, 'product', 1)"></i>
								<span class="like-amount-up">
									{{ $stat['likes']->where('is_up', 1)->count() }}
								</span>
							</td>
							<td width="20"></td>
							<td align="left" valign="middle">
								<i class="fa fa-thumbs-down like-btn {{ $has_like_down }}"
								   onclick="toggleLike(this, {{ $product->id }}, 'product', 0)"></i> 
								<span class="like-amount-down">
									{{ $stat['likes']->where('is_up', 0)->count() }}
								</span>
							</td>
						</tr>
					</table>
				</div>
				<h1>{{ $product->name }}</h1>
				<script> document.write('<span class="text-muted">{{ $product->name_original }}</span>'); </script>
				<br />
				<br />
				
				@if(!empty($product->description))
					@include('modules.spoiler', ['description' => $product->description])
					<br />
				@endif
				
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td align="left" valign="top">
							<ul class="list-unstyled">
								<li>
									<span class="text-muted">
										@if(sizeof($product->countries) > 1)
											Страны:
										@else
											Страна:
										@endif
									</span> 
									
									@foreach($product->countries as $val)
										{{ $val->name }}@if(!$loop->last), @endif
									@endforeach
								</li>
								<li><span class="text-muted">Год выпуска:</span> {{ $product->year }}</li>
								<li>
									<span class="text-muted">
										@if(sizeof($product->cats) > 1)
											Жанры:
										@else
											Жанр:
										@endif
									</span>
									@foreach($product->cats as $key => $val)
										<a href="/cats/{{ $val->slug }}">{{ $val->name }}</a>@if(!$loop->last), @endif
									@endforeach
								</li>
								<li><span class="text-muted">Продолжительность:</span> {{ doNormalDuration($product->duration) }}</li>
								<li><span class="text-muted">Бюджет:</span> {{ htmlPrice($product->budget) }} $</li>
							</ul>
						</td>
						<td width="20"></td>
						<td align="left" valign="top">
							<ul class="list-unstyled">
								<li><span class="text-muted">Рейтинг IMDb:</span> {{ $product->rating_imdb }}</li>
								<li><span class="text-muted">Рейтинг КиноПоиск:</span> {{ $product->rating_kinopoisk }}</li>
								<li>
									<span class="text-muted">Качество видео:</span> 
									{{ $product->quality_video_name }} 
									&nbsp;
									<i class="fa fa-info-circle activity" 
									   title="{{ $product->quality_video_description }}"></i>
								</li>
								<li>
									<span class="text-muted">Качество звука:</span> 
									{{ $product->quality_dubbing_name }}
									&nbsp;
									<i class="fa fa-info-circle activity" 
									   title="{{ $product->quality_dubbing_description }}"></i>
								</li>
								<li><span class="text-muted">Возраст:</span> {{ $product->old }}</li>
								<li>
									<i class="fa fa-eye fa-fw"></i>
									{{ $product->see + $stat['see_new'] }}
									
									@if(!empty($stat['see_new']))
										<span class="text-white">(+{{ $stat['see_new'] }})</span>
									@endif
									
									&nbsp;&nbsp;&nbsp;&nbsp;
									
									<i class="fa fa-comments fa-fw"></i>
									{{ $stat['total_comments'] }}
									
									@if(!empty($stat['total_comments_new']))
										<span class="text-white">(+{{ $stat['total_comments_new'] }})</span>
									@endif
								</li>
							</ul>
						</td>
					</tr>
				</table>
				
				@if(!empty($product->trailers) && is_array($product->trailers) && sizeof($product->trailers))
					<div class="tabs" style="position: relative; top: -5px">
						<div class="tabs-panel">
							@foreach($product->trailers as $key => $val)
								@if ($loop->first)
									<div class="tabs-panel-item active"><small>Трейлер №{{ $key+1 }}</small></div>
								@else
									<div class="tabs-panel-item"><small>Трейлер №{{ $key+1 }}</small></div>
								@endif
							@endforeach
						</div>
						<div class="tabs-content">
							@foreach($product->trailers as $key => $val)
								<div class="tabs-content-item">
									<iframe width="350" height="197" src="https://www.youtube.com/embed/{{ $val }}" frameborder="0" allowfullscreen></iframe>
								</div>
							@endforeach
						</div>
					</div>
					<br />
				@endif
			</div>
		</div>
	</div>
	<br />
	<hr />
	@if(	
			(!empty($product->producers) && !$product->producers->isEmpty()) 
			|| 
			(!empty($product->actors) && !$product->actors->isEmpty())
		)
		<div class="h4">Режиссеры и актеры:</div>
		<div id='slick-humans' class="slick">
			@foreach($product->producers as $producer)
				<div>
					@include('modules.human_thumb', ['human' => $producer])	
				</div>
			@endforeach

			@foreach($product->actors as $actor)
				<div>
					@include('modules.human_thumb', ['human' => $actor])	
				</div>
			@endforeach
		</div>
		<br />
	@endif
	@if(sizeof($product->frames))
		<div class="h4">Кадры из фильма:</div>
		<div id='slick-frames' class="slick">
			@foreach($product->frames as $key => $frame)
				<div>
					<img class="slick-frame" 
						 height="100" 
						 title="{{ $product->name }} - кадр {{ $key+1 }}" 
						 src="/images/sm_{{ $frame }}" 
						 data-original = "{{ $frame }}"
						 data-toggle = "modal" 
						 data-target = "#modalFrames"
						 data-key = "{{ $key }}" />
				</div>
			@endforeach
		</div>
		<br />
	@endif
	<br />
	
	<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td align="left" valign="bottom" width="180">
				@if(!is_null(Cookie::get('my_favorites')) && in_array($product->id, explode("|", Cookie::get('my_favorites'))))
					<span class="text-muted">Убрать из избранного:</span>
					<i class="fa fa-star text-orange activity" 
					   onclick="toggleMyFavorites(this, {{ $product->id }})"></i>
					
				@else
					<span class="text-muted">Добавить в избранное:</span>
					<i class="fa fa-star activity" 
					   onclick="toggleMyFavorites(this, {{ $product->id }})"></i>
				@endif
			</td>
			<td align="right" valign="bottom" width="*">
				@if(!empty($product->slogan))
					<span class="text-muted">слоган:</span>
					<span class="h3">"{{ $product->slogan }}"</span>
				@endif
			</td>
		</tr>
	</table>
	
	<div id="big-player" align="center">
		<object id="flash-player" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=11,0,0,0" width="640" height="390" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"> 
			<param name="allowScriptAccess" value="sameDomain"> 
			<param name="allowFullScreen" value="true"> 
			<param name="movie" value="/flash/pl.swf"> 
			<param name="loop" value="false"> 
			<param name="quality" value="high"> 
			<param name="flashvars" value="file_path={{ url('/download/'.$product->id) }}&logo_path=../img/logo-v1.png">
			<embed src="/flash/pl.swf" 
				   flashvars="file_path={{ url('/download/'.$product->id) }}&logo_path=../img/logo-v1.png" 
				   loop="false" quality="high" 
				   width="640" height="390" 
				   name="flash-player" 
				   allowscriptaccess="sameDomain" allowfullscreen="true" 
				   type="application/x-shockwave-flash"  
				   pluginspage="http://www.macromedia.com/go/getflashplayer" /> 
		</object>
	</div>
	<br />
	<hr />
	<br />
	
	@include('modules.comments', [
		'comments' => $comments,
		'opt' => 'product',
		'el_id' => $product->id,
		'h3' => 'А что Вы думаете о фильме?'
 	])
	
	<div id="modalFrames" class="modal">
		<div class="modal-wrapper">
			<i class="fa fa-close activity modal-wrapper-close" onclick="$(this).parent().parent().click()"></i>
			<div class="modal-content"></div>
		</div>
	</div>
	<div id="modalDownloadFlash" class="modal modal-sm">
		<div class="modal-wrapper">
			<i class="fa fa-close activity modal-wrapper-close" onclick="$(this).parent().parent().click()"></i>
			<div class="modal-content">
				<div class="as-tbl margin-auto">
					Для отображения видео необходим Flash-плеер.
					<br />
					Пройдите по ссылке, чтобы скачать его.
					<br />
					<br />
					<a href="https://get.adobe.com/ru/flashplayer/" target="_blank">https://get.adobe.com/ru/flashplayer/</a>
				</div>
			</div>
		</div>
	</div>
	
	<script>
		var isProductPage = true;
		
		function ready(){
			setTimeout(function(){
				$.post('/ajax-set-access-for-now-playing', {product_id: {{ $product->id }} });
			}, {{ config('my_constants.time_interval_for_now_playing') }} * 1000);
		}
		document.addEventListener("DOMContentLoaded", ready);
	</script>
@stop