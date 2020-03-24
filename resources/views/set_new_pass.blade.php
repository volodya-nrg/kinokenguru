@extends('layouts.main', [
	"title" => "Установка нового пароля"
])

@section('content')
	<form id="form-set-new-pass" class='as-tbl margin-auto' action="" method="post">
		<h2>Установка нового пароля</h2>
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
						Пароль:
					</td>
					<td align="left" valign="middle">
						<input class="my-input" type="password" name="pass" value="{{ old('pass') }}" />
					</td>
				</tr>
				<tr>
					<td align="left" valign="middle">
						Пароль (повтор):
					</td>
					<td align="left" valign="middle">
						<input class="my-input" type="password" name="pass_c" value="{{ old('pass_c') }}" />
					</td>
				</tr>
				<tr>
					<td align="left" valign="middle">
						{{ csrf_field() }}
					</td>
					<td align="left" valign="middle">
						<br />
						<input class="my-btn" type="submit" value="Установить" />
					</td>
				</tr>
			</table>
			<br />
		@endif

	</form>
@stop

