<div class="product-thumbnail-horiz">
	<div class="product-thumbnail-horiz-title text-eclipse">
		<a href="/{{ $product->slug }}">{{ $product->name }}</a> 
		<small class="text-muted">({{ $product->year }})</small>
	</div>
	<div class="product-thumbnail-horiz-cover" onclick="window.location.href='/{{ $product->slug }}'">
		<div class="product-thumbnail-horiz-cover-desc">
			<small>
				Жанр: 
				@foreach($product->cats as $key => $val)
					{{ $val->name }}@if(!$loop->last), @endif
				@endforeach
			</small>
		</div>
		<i class="fa fa-play fa-4x fa-fw"></i>
		
		@if (!empty($product->images[0]))
			<img width src="/images/hr_{{ $product->images[0] }}" /> 
		@endif	
	</div>
</div>