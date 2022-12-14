<div class="mb-3">
	<label class="form-label">{!! $label !!} @if($required) <star>*</star> @endif</label>
	<textarea type="text" name="{{$name}}" cols="4" id="{{$name}}"   @if($required) required="required" @endif class="form-control">{{$default}}</textarea> 
</div>