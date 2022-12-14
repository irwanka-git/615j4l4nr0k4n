<?php
$main_path = Request::segment(1);
loadHelper('akses'); 
?>
@extends('layout')
@section("pagetitle")
	 
@endsection

@section('content')

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Ganti Password</h5>
						<h6 class="card-subtitle text-muted">Fitur Ini Digunakan Untuk Mengganti Password User </h6>
					</div>
					<div class="card-body">
                        {{ Form::bsOpen('form-edit',url("update-password")) }}<br>
                        {{ Form::bsPassword('Password (Lama)','password1','',true,'md-8') }}<br>
                        {{ Form::bsPassword('Password (Baru)','password2','',true,'md-8') }}<br>
                        {{ Form::bsPassword('Ketik Ulang Password (Baru)','password3','',false,'md-8') }}<br>
                       <button type="submit" class="btn btn-primary">Simpan</button>
                    	{{ Form::bsClose()}}
                               

					</div>
				</div>
			</div>
		</div>
 
@endsection

@section("modal")
 

@endsection

@section("js")
<script type="text/javascript">
	$(function(){
		
        @if(Session::has('success'))
            swal('Berhasil',  '{{Session::get('success')}}')
        @endif

        @if(Session::has('error'))
            swal('Mohon Maaf', '{{Session::get('error')}}')
        @endif
 
	})
</script>
@endsection