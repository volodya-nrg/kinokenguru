@extends('layouts.main', [
	"title"			=> $title,
	"meta_keywords"	=> $meta_keywords,
	"meta_desc"		=> $meta_desc
])

@section('content')
	<h1>{{ $title }}</h1>
	
	@if(!empty($description))
		@include('modules.spoiler', ['description' => $description])
	@endif
	
	<br />
	<br />
	<div class="product-thumbnail-list">
		@each('modules.product_thumb', $aProducts, 'product')
	</div>

	@include('modules.modal_trailer')
@stop