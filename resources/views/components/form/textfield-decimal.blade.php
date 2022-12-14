<div class="mb-3">
	<label class="form-label">{!! $label !!} @if($required) <star>*</star> @endif</label>
	<input type="text" negatif="{{$negatif}}"  name="{{$name}}" id="{{$name}}" value="{{$default}}" @if($required) required="required" @endif placeholder="0.00" class="form-control decimal">
</div>