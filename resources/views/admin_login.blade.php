<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" 	content="IE=edge">
    <meta name="viewport" 				content="width=device-width, initial-scale=1">
	
	<link rel="icon" type="image/x-icon"   href="/img/favicon.png" />
	
	<link rel="stylesheet" type="text/css" href="/css/all.min.css" />
    
    <title>Администрирование</title>
</head>
<body>
	<header>
		<a class="as-tbl" href="/" style="margin: 10px 0"><img height="50" src="/img/logo-v2.png" /></a>
	</header>
	<main>
		<form id="form-admin" class='as-tbl margin-auto' action="" method="post">
			<h2>Вход в админ. панель</h2>
			<br />

			@if(session()->has('errors'))
				<ul>
					@foreach(session('errors') as $val)
						<li class='text-red'>{{ $val }}</li>
					@endforeach
				</ul>
				<br />
			@endif

			<table cellspacing="5" cellpadding="0" width="395">
				<tr>
					<td align="left" valign="middle" width="170">
						Логин:
					</td>
					<td align="left" valign="middle" width="*">
						<input class="my-input" type="text" name="login" value="{{ old('login') }}" maxlength="255" />
					</td>
				</tr>
				<tr>
					<td align="left" valign="middle">
						Пароль:
					</td>
					<td align="left" valign="middle">
						<input class="my-input" type="password" name="pass" value="" />
					</td>
				</tr>
				<tr>
					<td align="left" valign="middle">
						{{ csrf_field() }}
					</td>
					<td align="left" valign="middle">
						<br />
						<input class="my-btn" type="submit" value="Авторизоваться" />
					</td>
				</tr>
			</table>
			<br />
		</form>
	</main>
</body>
</html>