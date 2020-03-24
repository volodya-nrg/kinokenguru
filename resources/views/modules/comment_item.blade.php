<div class="comment-item">
	@if(is_file(config('my_constants.dir_images').'/avatar_sm_'.$item->user_id.'.jpg'))
		<img class="comment-item-avatar" src="/images/avatar_sm_{{ $item->user_id }}.jpg" />
		
	@else
		<img class="comment-item-avatar" src="/img/user_empty.jpg" />
	@endif
	
	<div class="comment-item-title">
		<span class="pull-left">
			@if(!empty($item->name))
				<strong>{{ $item->name }}</strong>
			@endif
			
			@if(!empty($item->name) && !empty($item->email))
				&nbsp;<span class="text-muted">|</span>&nbsp; 
			@endif
			
			@if(!empty($item->email))
				<a class="a-gray-md" href="mailto:{{ $item->email }}">{{ $item->email }}</a>
			@endif
		</span>

		@if(
				( session()->has('user_id') && ($item->user_id === session('user_id')) )
				||
				( session()->getId() === $item->ses_id )
			)
			<i class="comment-item-remove fa fa-times-circle activity" 
			   title="удалить" onclick="removeComment(this, {{ $item->id }}, 'product')"></i>	
		@endif
		
		<small class="text-muted">{{ date("Y-m-d, H:i", strtotime($item->created_at)) }}</small>
	</div>
	<div class="comment-item-text">
		{!! nl2br($item->text) !!}
	</div>
</div>