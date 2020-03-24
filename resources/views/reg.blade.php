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
	
	<form id="form-reg" class='as-tbl margin-auto' action="" method="post">
		<h2>Регистрация</h2>
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

		<table cellspacing="5" cellpadding="0" width="395">
			<tr>
				<td align="left" valign="middle" width="170">
					Е-мэйл: *
				</td>
				<td align="left" valign="middle" width="*">
					<input class="my-input" type="text" name="email" value="{{ old('email') }}" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle">
					Пароль: *
				</td>
				<td align="left" valign="middle">
					<input class="my-input" type="password" name="pass" value="{{ old('pass') }}" />
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle">
					Пароль (повтор): *
				</td>
				<td align="left" valign="middle">
					<input class="my-input" type="password" name="pass_c" value="{{ old('pass_c') }}" />
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle">
					Имя:
				</td>
				<td align="left" valign="middle">
					<input class="my-input" type="text" name="name" value="{{ old('name') }}" maxlength="255" />
					<br />
					<small class="text-muted">* поля обязательны для заполнения</small>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle">
				</td>
				<td align="left" valign="middle">
					<input type="checkbox" name="agree" {{ old('agree')? 'checked': '' }} />&nbsp;&nbsp; согласен с <a href="/infos/polzovatelskoe-soglashenie" target="_blank">правилами</a> *
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle">
					{{ csrf_field() }}
				</td>
				<td align="left" valign="middle">
					<br />
					<input class="my-btn" type="submit" value="Зарегистрироваться" />
				</td>
			</tr>
		</table>
		<br />
	</form>
@stop

