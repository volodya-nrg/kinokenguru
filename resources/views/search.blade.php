@extends('layouts.main', [
	"title"			=> $title,
	"meta_keywords"	=> $meta_keywords,
	"meta_desc"		=> $meta_desc
])

@section('content')
	@if(!empty($description))
		@include('modules.spoiler', ['description' => $description])
	@endif

	@if (!$is_serched)
		<table width="100%" height="400"><tr><td align="center" valign="middle">
			<span class="text-muted">введите поисковую фразу в соответсвующее поле сверху</span>
		</td></tr></table>
	
	@else
		@if (sizeof($aProducts))
			<div class="product-thumbnail-list">
				@each('modules.product_thumb', $aProducts, 'product')
			</div>
			
			@include('modules.modal_trailer')
			
		@else
			<table width="100%" height="400"><tr><td align="center" valign="middle">
				<span class="text-muted">не найдено</span>
			</td></tr></table>
		@endif
	@endif
@stop