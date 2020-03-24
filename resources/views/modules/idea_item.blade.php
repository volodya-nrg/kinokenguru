<div class="idea-item">
	<div class="idea-item-status">
		@if($item->status === "ok")
			<i class="fa fa-check fa-2x fa-fw text-green"></i>
			
		@elseif($item->status === "no")
			<i class="fa fa-times fa-2x fa-fw text-red"></i>
			
		@else
			<i class="fa fa-hourglass fa-2x fa-fw"></i>
		@endif
	</div>
	<div class="idea-item-content">
		<div class="idea-item-info text-muted">
			Статус:

			@if($item->status === "ok")
				выполнено

			@elseif($item->status === "no")
				отказ

			@else
				ожидание
			@endif
				
			@if(session()->getId() === $item->ses_id)
				<i class="idea-item-remove fa fa-times-circle fa-fw activity" title="удалить"  
				   onclick="removeIdea(this, {{ $item->id }})"></i>	
			@endif

			<small>{{ date("Y-m-d, H:i", strtotime($item->created_at)) }}</small>
		</div>
		<div class="idea-item-idea">
			{!! nl2br($item->text) !!}
		</div>
	
		@if(!empty($item->answer))
			<div class="idea-item-answer">
				<table cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td class="text-white" align="left" valign="top" width="42">
							Ответ:
						</td>
						<td width="20"></td>
						<td align="left" valign="top">
							{!! nl2br($item->answer) !!}
						</td>
					</tr>
				</table>
			</div>
		@endif

	</div>
</div>