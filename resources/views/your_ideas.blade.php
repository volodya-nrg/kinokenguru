@extends('layouts.main', [
	"title"			=> $title,
	"meta_keywords"	=> $meta_keywords,
	"meta_desc"		=> $meta_desc
])

@section('content')
	
	@if(sizeof($ideas) > 3)
		<div align="right">
			<a href="#form-idea">написать</a>
		</div>
	@endif
	
	@if(!empty($description))
		@include('modules.spoiler', ['description' => $description])
		<br />
	@endif
	
	<br />
	<br />
	<div id="list-ideas">
		<center id="idea-items-empty" @if(sizeof($ideas)) class="hide" @endif >
			<span class="text-muted">Напиши рекомендацию первым!</span>
			<br />
			<br />
			<br />
		</center>
		
		@if(sizeof($ideas))
			@each('modules.idea_item', $ideas, 'item')
		@endif
	</div>
	<br />
	<form id="form-idea" onsubmit="return false">
		<table cellspacing="5" cellpadding="0" width='100%'>
			<tr>
				<td width="170"></td>
				<td align="left">
					<div class="alert hide"></div>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top">
					<span class="text-muted">Рекомендация:</span>
				</td>
				<td align="left" valign="middle">
					<textarea class="my-textarea textarea-block" name='text' rows="5"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<input class="hide" type="reset" />
				</td>
				<td align="left" valign="middle">
					<br />
					<input class="my-btn" type="submit" value="Добавить" onclick="addIdea(this)" />
				</td>
			</tr>
		</table>	
	</form>
@stop