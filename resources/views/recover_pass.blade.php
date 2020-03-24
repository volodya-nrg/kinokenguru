@extends('layouts.main', [
	"title"			=> $title,
	"meta_keywords"	=> $meta_keywords,
	"meta_desc"		=> $meta_desc
])

@section('content')
	@if(!empty($description))
		@include('modules.spoiler', ['description' => $description])
		<br />
	@endif
	
	<form id="form-recover-pass" class='as-tbl margin-auto' action="" method="post">
		<h2>Восстановление пароля</h2>
		<br />

		@if(session()->has('errors'))
			<ul>
				@foreach(session('errors') as $val)
					<li class='text-red'>{{ $val }}</li>
				@endforeach
			</ul>
			<br />
		@elseif(session()->has('ok'))
			<div class='text-green'>{!! session('ok') !!}</div>
			<br />
		@endif

		@if(!session()->has('ok'))
			<table cellspacing="5" cellpadding="0" width="395">
				<tr>
					<td align="left" valign="middle" width="170">
						Е-мэйл:
					</td>
					<td align="left" valign="middle">
						<input class="my-input" type="text" name="email" value="{{ old('email') }}" maxlength="255" />
					</td>
				</tr>
				<tr>
					<td align="left" valign="middle">
						{{ csrf_field() }}
					</td>
					<td align="left" valign="middle">
						<br />
						<input class="my-btn" type="submit" value="Отправить" />
					</td>
				</tr>
			</table>
			<br />
		@endif

	</form>
@stop