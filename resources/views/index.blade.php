@extends('layouts.main', [
	"title"			=> $title,
	"meta_keywords"	=> $meta_keywords,
	"meta_desc"		=> $meta_desc
])

@section('content')
	<table width="100%">
    	<tr>
        	<td align="left" valign="top" width="171">
                <aside id="left-menu">
					<strong>Категории:</strong>
                    
					@if (!empty($aCats))
						<ul>
							@foreach($aCats as $cat)
								<li>
									<a href="/cats/{{ $cat->slug }}">{{ $cat->name }}</a> 
									<small class="text-muted">({{ $cat->total }})</small>
								</li>
							@endforeach
						</ul>
						<hr />
					@endif
					
					<strong>Подписаться на обновления:</strong>
					<form id="form-subscribe" onsubmit="return false">
						<input class="hide" type="reset" />
						<input class="my-input" type="text" name="email" placeholder="е-мэйл" />
						<button class="my-btn" onclick="addSubscriber(this)">
							<i class="fa fa-envelope fa-fw"></i>
						</button>
						<div class="alert"></div>
					</form>
					
                </aside>
            </td>
            <td align="left" valign="top">
				<h1>Смотреть кино онлайн</h1>
				
				@if(!empty($description))
					@include('modules.spoiler', ['description' => $description])
				@endif
				
				<br />
				
				@if (!empty($aLastProducts))
					<div class="h2">Новое на сайте:</div>
					<div class="afisha">
						@each('modules.afisha_item', $aLastProducts, 'product')
					</div>
				@endif
            </td>
        </tr>
    </table>
	<br />
	<hr />  
    <br />
	@if (!empty($aNowWatchProducts))
		<div class="h3">Фильмы которые смотрят сейчас:</div>
		<div>
			@each('modules.product_thumb_horiz', $aNowWatchProducts, 'product')
		</div>
	@endif
@stop