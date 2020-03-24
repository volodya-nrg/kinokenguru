<div class="product-thumbnail">
	<a class="product-thumbnail-cover" href="/{{ $product->slug }}">
		<div class="product-thumbnail-title">
			<div class="text-eclipse">{{ $product->name }} ({{ $product->year }})</div>
			<small class="text-white">
				@foreach($product->cats as $key => $val)
					{{ $val->name }}@if(!$loop->last), @endif
				@endforeach
			</small>
		</div>
		
		@if(!empty($product->images[0]))
			<img src="/images/md_{{ $product->images[0] }}" />
		@endif
		
		<div class="product-thumbnail-short-desc">
			{!! str_limit($product->description, 300) !!}
		</div>
	</a>
	<div class="product-thumbnail-desc">
		<table cellpading="0" cellspasing="0" width="100%">
			<tr>
				<td valign="middle" width="100">
					<i class="fa fa-imdb fa-1x text-white" title="Рейтинг IMDB"></i>
					{{ $product->rating_imdb }}
					&nbsp;
					<i class="fa fa-futbol-o fa-1x text-white" title="Рейтинг КиноПоиск.ру"></i>
					{{ $product->rating_kinopoisk }}
				</td>
				<td width='10'></td>
				<td align="center" valign="middle" width="12">
					<i class="fa fa-info-circle fa-1x" 
					   onmouseover="$(this).closest('.product-thumbnail').find('.product-thumbnail-short-desc').show()" 
					   onmouseout="$(this).closest('.product-thumbnail').find('.product-thumbnail-short-desc').hide()" ></i>
				</td>
				<td width='*'></td>
				
				@if(sizeof($product->trailers))
					<td align="right" valign="middle" width="80">
						трейлер
						&nbsp;
						<a href="javascript: void(0)" 
						   data-toggle = "modal" 
						   data-target = "#modalTrailers" 
						   data-trailers = "{{ implode(",", $product->trailers) }}"
						   data-name = "{{ $product->name }}"
						   ><i class="fa fa-youtube-play fa-1x"></i></a>
					</td>
				@endif
			</tr>
		</table>
	</div>
</div>