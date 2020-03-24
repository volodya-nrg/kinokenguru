@extends('layouts.main', [
	"title" => "Подтверждение е-мэйла"
])

@section('content')
	<table width="100%" height="400"><tr><td align="center" valign="middle">
		<h2 class="text-green">Ваш е-мэйл ({{ $email }}) подтвержден!</h2>
		<br />
		<h3>Добро пожаловать на сайт.</h3>
	</td></tr></table>
@stop