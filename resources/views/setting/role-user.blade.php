<?php
$main_path = Request::segment(1);
use App\Library\Access;
?>
@extends('layout')
@section("pagetitle")
{{ $pagetitle }}
@endsection

@section('content')

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">{{ $pagetitle }}</h5>
						<h6 class="card-subtitle text-muted">{{ $smalltitle }}</h6>
					</div>
					<div class="card-body">
						<a href="{{url('setting-role')}}" class="btn btn-secondary"><i class="la la-arrow-left"></i> Kembali</a>
						@if(Access::UserCanCreate())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah '.$role->nama_role,'modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th>Nama Pengguna</th>
									<th width="25%">Email</th>
									<th width="20%">Nomor Telepon</th>
									<th width="10%">Actions</th>
								</tr>
							</thead>
							<tbody>
								 
							</tbody>
						</table>


					</div>
				</div>
			</div>
		</div>
 
@endsection

@section("modal")
@if(Access::UserCanCreate())
<!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-tambah',url($main_path."/insert-user")) }}
	{{Html::mOpenLG('modal-tambah','Tambah Akun '.$role->nama_role)}}
		{{ Form::bsTextField('Nama Pengguna','name','',true,'md-8') }}
		{{ Form::bsTextField('Email','email','',true,'md-8') }}
		{{ Form::bsTextField('Nomor Telepon','phone','',true,'md-8') }}
		{{ Form::bsTextField('Password','password','',true,'md-8') }}
		{{ Form::bsHidden('id_role',$role->id) }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(Access::UserCanUpdate())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update-user")) }}
	{{Html::mOpenLG('modal-edit','Edit Akun '.$role->nama_role)}}
		{{ Form::bsTextField('Nama Pengguna','name','',true,'md-8') }}
		{{ Form::bsTextField('Email','email','',true,'md-8') }}
		{{ Form::bsTextField('Nomor Telepon','phone','',true,'md-8') }}
		{{ Form::bsTextField('Password','password','',false,'md-8') }}
		{{ Form::bsCheckSwitch('Ganti Password','change_password','1',true,'md-8') }}
		{{ Form::bsHidden('uuid','') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(Access::UserCanDelete())
 <!-- FORM DELETE -->
{{ Form::bsOpen('form-delete',url($main_path."/delete-user")) }}
	{{ Form::bsHidden('uuid','') }}
{{ Form::bsClose()}}
@endif

@endsection

@section("js")
<script type="text/javascript">
	$(function(){
		 

		var $tabel1 = $('#datatable').DataTable({
		    processing: true,
		    responsive: true,
		    fixedHeader: true,
		    serverSide: true,
		    ajax: "{{url('setting-role/dt-user/'.$role->uuid)}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'name' , name:"name" , orderable:true, searchable: false,sClass:""},
				 {data:'email' , name:"email" , orderable:false, searchable: false,sClass:""},
		         {data:'phone' , name:"phone" , orderable:false, searchable: false,sClass:""},
		         {data:'action' , orderable:false, searchable: false,sClass:"text-center"},
		        ],
		        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
		        $(nRow).addClass( aData["rowClass"] );
		        return nRow;
		    },
		    "drawCallback": function( settings ) {
		        @if(Access::UserCanDelete())
		        initKonfirmDelete();
		        @endif
		    }
		});

		@if(Access::UserCanCreate())
		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e){
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm();
			enableButton("#form-tambah button[type=submit]")
		});

		$('#form-tambah').ajaxForm({
			beforeSubmit:function(){disableButton("#form-tambah button[type=submit]")},
			success:function($respon){
				if ($respon.status==true){
					 $("#modal-tambah").modal('hide'); 
					 successNotify($respon.message);
					 $tabel1.ajax.reload(null, true);
				}else{
					errorNotify($respon.message);
				}
				enableButton("#form-tambah button[type=submit]")
			},
			error:function(){
				$("#form-tambah button[type=submit]").button('reset');
				$("#modal-tambah").modal('hide'); 
				errorNotify('Terjadi Kesalahan!');
			}
		}); 
		@endif

		@if(Access::UserCanUpdate())
		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]")
			$.get("{{url('setting-role/get-user')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #uuid').val(respon.data.uuid);
					$('#form-edit #name').val(respon.data.name);
					$('#form-edit #email').val(respon.data.email);
					$('#form-edit #phone').val(respon.data.phone);
					$('#form-edit #password').val('');
					$('#form-edit #change_password').prop('checked',false);
					enableButton("#form-edit button[type=submit]");
				}else{
					errorNotify(respon.message);
				}
			})
		});

		$('#form-edit').ajaxForm({
			beforeSubmit:function(){disableButton("#form-edit button[type=submit]")},
			success:function($respon){
				if ($respon.status==true){
					 $("#modal-edit").modal('hide'); 
					 successNotify($respon.message);
					 $tabel1.ajax.reload(null, true);
				}else{
					errorNotify($respon.message);
				}
				enableButton("#form-edit button[type=submit]")
			},
			error:function(){
				$("#form-edit button[type=submit]").button('reset');
				$("#modal-edit").modal('hide'); 
				errorNotify('Terjadi Kesalahan!');
			}
		}); 
		@endif

		@if(Access::UserCanDelete())
		$('#form-delete').ajaxForm({
			beforeSubmit:function(){},
			success:function($respon){
				if ($respon.status==true){
					 successNotify($respon.message);
					 $tabel1.ajax.reload(null, true);
				}else{
					errorNotify($respon.message);
				}
			},
			error:function(){errorNotify('Terjadi Kesalahan!');}
		}); 
		var initKonfirmDelete= function(){
			$('.btn-konfirm-delete').on('click', function(e){
				$uuid  = $(this).data('uuid');
				 
				$.get("{{url('setting-role/get-user')}}/"+$uuid, function(respon){
					if(respon.status){
						$("#form-delete #uuid").val(respon.data.uuid);
						$.confirm({
						    title: 'Yakin Hapus Akun Ini?',
						    content: respon.informasi,
						    buttons: {
						        
						        cancel :{
						        	text: 'Batalkan'
						        },
						        confirm: {
						        	text: 'Hapus',
						        	btnClass: 'btn-danger',
						        	action:function(){$("#form-delete").submit()}
						        },
						    }
						});
					}else{
						errorNotify(respon.message);
					}
				})
			})
		}
		@endif

		
			 

	})
</script>
@endsection