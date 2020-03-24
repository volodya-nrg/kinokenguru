<div class="h3">Комментарии:</div>
<div id="comment-items">
	<center id="comment-items-empty" @if(sizeof($comments)) class="hide" @endif >
		<span class="text-muted">Напиши комментарий первым!</span>
		<br />
		<br />
		<br />
	</center>

	@if(sizeof($comments))
		@each('modules.comment_item', $comments, 'item')
	@endif
</div>
<br />
<div class="h3">{{ $h3 }}</div>
<form id="form-comment" onsubmit="return false">
	<table border="0" cellspacing="5" cellpadding="0" width="100%">
		<tr><td width="170"></td><td></td></tr>
		
		@if(session()->has('user_id') === false)
			<tr>
				<td align="left" valign="middle">
					<span class="text-muted">Имя:</span>
				</td>
				<td align="left" valign="middle">
					<input class="my-input" type="text" name="name" maxlength="50" />
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle">
					<span class="text-muted">Е-мэйл:</span>
				</td>
				<td align="left" valign="middle">
					<input class="my-input" type="text" name="email" maxlength="255" />
				</td>
			</tr>
		@endif

		<tr>
			<td align="left" valign="top">
				<span class="text-muted">Коммент:</span> *
			</td>
			<td align="left" valign="middle">
				<textarea class="my-textarea textarea-block" name='text' rows="5"></textarea>
				<br />
				<small class="text-muted">* поля обязательны для заполнения</small>
			</td>
		</tr>
		<tr>
			<td align="left" valign="middle">
				<input class="hide" type="reset" />
				<input type="hidden" name="opt" value="{{ $opt }}" />
				<input type="hidden" name="el_id" value="{{ $el_id }}" />
			</td>
			<td align="left" valign="middle">
				<br />
				<input class="my-btn" type="submit" value="Добавить комментарий" onclick="addComment(this)" />
			</td>
		</tr>
	</table>	
</form>