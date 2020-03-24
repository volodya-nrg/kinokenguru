@if(!empty($name))
	<h1>Здравствуйте, {{ $name }}!</h1>

@else
	<h1>Здравствуйте!</h1>
@endif

<p>Вы зарегистрировались на сайте <strong>{{ config("my_constants.domain") }}</strong>. Пожалуйста, подтвердите его, нажав на ссылку ниже.</p>
<p>
	<a href='{{ url('/reg/confirm-email/'.$secret) }}'>
		<font size="+1">{{ url('/reg/confirm-email/'.$secret) }}</font>
	</a>	
</p>
<small>Вы получили это письмо, т.к. ваш адрес электронной почты был указан на сайте {{ config("my_constants.domain") }}. Если вы этого не делали, проигнорируйте это письмо.</small>
<br />
<small>С уважением, команда {{ config("my_constants.email_signature") }}.</small>