@extends('layouts.main', [
	"title"			=> $title,
	"meta_keywords"	=> $meta_keywords,
	"meta_desc"		=> $meta_desc
])

@section('content')
	@if (sizeof($aProducts))
		<div class="product-thumbnail-list">
			@each('modules.product_thumb', $aProducts, 'product')
		</div>
		
		@include('modules.modal_trailer')
	@else
		<table width="100%" height="400"><tr><td align="center" valign="middle">
			<span class="text-muted">избранных фильмов пока нет</span>
		</td></tr></table>
	@endif
@stop