@extends('layouts.main')

@section('content')
<form id="form-profile-settings" action="" method="post" enctype="multipart/form-data">
	<h2>Настройки</h2>
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
	
	<table cellspacing="5" cellpadding="0">
		<tr>
			<td align="left" valign="middle" width="170">
				Аватар:
			</td>
			<td align="left" valign="middle">
				<input class="my-input-file" type="file" name="avatar" />
			</td>
			<td align="left" valign='top' rowspan="10">
				@if(is_file(config('my_constants.dir_images').'/avatar_'.session('user_id').'.jpg'))
					<div class="img-thumbnail">
						<div class="img-thumbnail-close" onclick="
							$(this).parent().fadeOut('fast', function(){
								$(this).remove();
							});">
							<i class="fa fa-close fa-fw"></i>
						</div>
						<img src='/images/avatar_{{ session('user_id') }}.jpg' />
						<input type="hidden" name="has_avatar" value="1" />
					</div>
					
				@else
					<div class="img-thumbnail">
						<img src='/img/user_empty.jpg' />
					</div>
				@endif
			</td>
		</tr>
		<tr>
			<td align="left" valign="middle">
				Е-мэйл:
			</td>
			<td align="left" valign="middle">
				<input class="my-input" type="text" disabled value="{{ $email }}" />
			</td>
		</tr>
		<tr>
			<td align="left" valign="middle">
				Имя:
			</td>
			<td align="left" valign="middle">
				<input class="my-input" type="text" name="name" value="{{ $name }}" maxlength="255" />
			</td>
		</tr>
		<tr height="30">
			<td align="left" valign="middle">
				
			</td>
			<td align="left" valign="middle">
				<a class="a-gray-md" href="javascript: $('.form-profile-settings-hidden-tr').toggle(); void(0);">изменить пароль</a>
			</td>
		</tr>
		<tr class="form-profile-settings-hidden-tr">
			<td align="left" valign="middle">
				Старый пароль:
			</td>
			<td align="left" valign="middle">
				<input class="my-input" type="password" name="old_pass" value="" />
			</td>
		</tr>
		<tr class="form-profile-settings-hidden-tr">
			<td align="left" valign="middle">
				Новый пароль:
			</td>
			<td align="left" valign="middle">
				<input class="my-input" type="password" name="new_pass" value="" />
			</td>
		</tr>
		<tr class="form-profile-settings-hidden-tr">
			<td align="left" valign="middle">
				Новый пароль (повтор):
			</td>
			<td align="left" valign="middle">
				<input class="my-input" type="password" name="new_pass_confirm" value="" />
			</td>
		</tr>
		<tr>
			<td align="left" valign="middle">
				{{ csrf_field() }}
			</td>
			<td align="left" valign="middle">
				<br />
				<input class="my-btn" type="submit" value="Обновить" />
			</td>
		</tr>
	</table>
	<br />
</form>
@stop

