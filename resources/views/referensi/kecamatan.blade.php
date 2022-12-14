<?php
$main_path = Request::segment(1);
use App\Library\Access;
?>
@extends('layout')
@section("pagetitle")
	{{$pagetitle}}
@endsection

@section('content')

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">{{$pagetitle}}</h5>
						<h6 class="card-subtitle text-muted">{{$smalltitle}} </h6>
					</div>
					<div class="card-body">
						@if(Access::UserCanCreate())
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Kecamatan','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th>#</th>
									<th width="10%">Kode</th>
									<th>Nama Kecamatan</th>
									<th width="20%">Kabupaten</th>
									<th>Actions</th>
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
{{ Form::bsOpen('form-tambah',url($main_path."/insert")) }}
	{{Html::mOpenLG('modal-tambah','Tambah Referensi Kecamatan')}}
		{{ Form::bsSelect2('Kabupaten','kode_kabupaten',$list_kabupaten,'',true,'md-8')}}
		<small>Kode Kecamatan : Kode Kabupaten (4 Digit) + Index Kecamatan (3 Digit)</small>
		{{ Form::bsTextField('Kode Kecamatan (7 Digit)','kode_kecamatan','',true,'md-8') }}
		{{ Form::bsTextField('Nama Kecamatan','nama_kecamatan','',true,'md-8') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(Access::UserCanUpdate())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Edit Referensi Kecamatan')}}
		{{ Form::bsSelect2('Kabupaten','kode_kabupaten',$list_kabupaten,'',true,'md-8')}}
		<small>Kode Kecamatan : Kode Kabupaten (4 Digit) + Index Kecamatan (3 Digit)</small>
		{{ Form::bsTextField('Kode Kecamatan (7 Digit)','kode_kecamatan','',true,'md-8') }}
		{{ Form::bsTextField('Nama Kecamatan','nama_kecamatan','',true,'md-8') }}
		{{ Form::bsHidden('uuid','') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
 @endif

 @if(Access::UserCanDelete())
 <!-- FORM DELETE -->
{{ Form::bsOpen('form-delete',url($main_path."/delete")) }}
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
		    ajax: "{{url($main_path.'/dt')}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'kode_kecamatan' , name:"kode_kecamatan" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'nama_kecamatan' , name:"nama_kecamatan" , orderable:true, searchable: false,sClass:""},		         
		         {data:'nama_kabupaten' , name:"nama_kabupaten" , orderable:true, searchable: false,sClass:""},
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
			$('#form-tambah #kode_kabupaten').selectize()[0].selectize.clear();
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
			error:function($respon){
				$("#form-tambah button[type=submit]").button('reset');
				$("#modal-tambah").modal('hide'); 
				errorNotify($respon.message);
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
			$('#form-edit #kode_kabupaten').selectize()[0].selectize.clear();
			$.get("{{url($main_path.'/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #uuid').val(respon.data.uuid);
					$('#form-edit #kode_kecamatan').val(respon.data.kode_kecamatan);
					$('#form-edit #nama_kecamatan').val(respon.data.nama_kecamatan);
					$('#form-edit #kode_kabupaten').selectize()[0].selectize.setValue(respon.data.kode_kabupaten,false);
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
				 
				$.get("{{url($main_path.'/get-data')}}/"+$uuid, function(respon){
					if(respon.status){
						$("#form-delete #uuid").val(respon.data.uuid);
						$.confirm({
						    title: 'Yakin Hapus Data?',
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