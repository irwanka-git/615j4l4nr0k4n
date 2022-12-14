<div class="form-group">
	<div class="col-md-3 text-right">
		<span>{{$label}}</span>
		@if($required) <star>*</star> @endif
	</div>
	<div class="col-{{$md_size}}">
		<input type="text" min="{{$negatif}}" max="{{$negatif}}" negatif="{{$negatif}}" name="{{$name}}"  
			id="{{$name}}" value="{{$default}}" @if($required) required="required" @endif class="form-control decimal">
	</div>
</div>