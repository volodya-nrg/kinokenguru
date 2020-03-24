<div class="afisha-item">
	<div class="afisha-item-content">
		<h3><a href="/{{ $product->slug }}">{{$product->name}}</a> ({{ $product->year }})</h3>
		<br />
		{!! str_limit($product->description, 500) !!}
	</div>
	<div class="afisha-item-cover">
		@if (!empty($product->images[0]))
			<img src="/images/md_{{ $product->images[0] }}" /> 
		@endif
	</div>
</div>