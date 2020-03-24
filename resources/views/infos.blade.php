@extends('layouts.main', [
	"title"			=> $title,
	"meta_keywords"	=> $meta_keywords,
	"meta_desc"		=> $meta_desc
])

@section('content')
	<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td id="infos-tree" align="left" valign="top">
				<div class="h4">Каталоги:</div>
				<br />
				{!! $tree !!}
			</td>
			<td width="20"></td>
			<td align="left" valign="top" width="*">
				<ul id="bread-crumbs">
					@php
						$save = "";
					@endphp
					
					<li><a class="a-gray-sm" href="/"><i class="fa fa-home"></i></a></li>
					
					@foreach($data['breadcrumbs'] as $key => $val)
						@if($key === sizeof($data['breadcrumbs'])-1)
							<li>{{ current($val) }}</li>
						
						@else
							@php
								$save .= "/".key($val);
							@endphp
							
							<li><a class="a-gray-sm" href="{{ $save }}">{{ current($val) }}</a></li>
						@endif
					@endforeach
				</ul>
				<br />
				{{-- если это не статья, а папка (подпапка или главная) --}}
				@if(is_null($data['post']))
					
					@if(!empty($description))
						@include('modules.spoiler', ['description' => $description])
						<br />
					@endif

					@if($data['dirs']->isEmpty() === false)
						<div class="h3 text-muted">Подкатегории:</div>
						<div class="as-tbl-100">
							@foreach($data['dirs'] as $val)
								<div class="folder-item">
									<img class="folder-item-cover" src='/img/folder.png' />
									<div class="text-eclipse">
										<a class="a-gray-sm" href="/{{ Request::path() }}/{{ $val->slug }}">{{ $val->name }}</a>
									</div>
								</div>
							@endforeach
						</div>
						<br />
					@endif

					@if($data['files']->isEmpty() === false)
						<div class="h3 text-muted">Статьи:</div>
						<ul>
							@foreach($data['files'] as $val)
								<li><a class="a-gray-sm" href="/{{ Request::path() }}/{{ $val->slug }}">{{ $val->title }}</a></li>
							@endforeach
						</ul>
						<br />
					@endif

				@else
					<h1>{{ $data['post']->h1 }}</h1>
					<div>
						{!! $data['post']->description !!}
					</div>
				@endif
			</td>
		</tr>
	</table>	
@stop