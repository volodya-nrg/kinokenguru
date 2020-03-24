<div class='human-item'>
	<div class="human-item-img">
		@if(!empty($human->images[0]))
			<img src="/images/sm_{{ $human->images[0] }}" />
		@endif
	</div>
	<div class="human-item-kind text-muted">
		<small>
			@if(!empty($human->is_producer))
				режиссер
			@else
				актер
			@endif	
		</small>
	</div>
	<div class="human-item-name">
		<small>{{ $human->fio_ru }}</small>
	</div>
</div>