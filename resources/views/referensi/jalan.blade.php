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
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah Data Ruas Jalan','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="2%">#</th>
									<th width="10%">Kode</th>
									<th>Nama Ruas Jalan</th>
									<th width="20%">Klasifikasi</th>
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
	{{Html::mOpenLG('modal-tambah','Tambah Referensi Ruas Jalan')}}
		{{ Form::bsSelect2('Klasfikasi','id_klasifikasi',$list_klasifikasi_jalan,'',true,'md-8')}}
		<small>Kode Jalan : Kode Klasifikasi (1 Digit) + Index Jalan (3 Digit)</small>
		{{ Form::bsTextField('Kode Jalan (4 Digit)','kode_jalan','',true,'md-8') }}
		{{ Form::bsTextField('Nama Ruas Jalan','nama_ruas_jalan','',true,'md-8') }}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(Access::UserCanUpdate())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG('modal-edit','Edit Referensi Ruas Jalan')}}
		{{ Form::bsSelect2('Klasfikasi','id_klasifikasi',$list_klasifikasi_jalan,'',true,'md-8')}}
		<small>Kode Jalan : Kode Klasifikasi (1 Digit) + Index Jalan (3 Digit)</small>
		{{ Form::bsTextField('Kode Jalan (4 Digit)','kode_jalan','',true,'md-8') }}
		{{ Form::bsTextField('Nama Ruas Jalan','nama_ruas_jalan','',true,'md-8') }}
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
		         {data:'kode_jalan' , name:"kode_jalan" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'nama_ruas_jalan' , name:"nama_ruas_jalan" , orderable:true, searchable: false,sClass:""},		         
		         {data:'nama_klasifikasi' , name:"nama_klasifikasi" , orderable:true, searchable: false,sClass:""},
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
			$('#form-tambah #id_klasifikasi').selectize()[0].selectize.clear();
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

		$("#form-tambah #id_klasifikasi").on('change', function(){
			$id_klasifikasi = $(this).val();
			if($id_klasifikasi){
				$.get("{{url($main_path)}}/generate-kode-jalan/"+$id_klasifikasi, function(respon){
					$("#form-tambah #kode_jalan").val(respon.kode_jalan);
				})
			}
		})
		@endif

		@if(Access::UserCanUpdate())
		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			disableButton("#form-edit button[type=submit]")
			$('#form-edit #id_klasifikasi').selectize()[0].selectize.clear();
			$.get("{{url($main_path.'/get-data')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #uuid').val(respon.data.uuid);
					$('#form-edit #kode_jalan').val(respon.data.kode_jalan);
					$('#form-edit #nama_ruas_jalan').val(respon.data.nama_ruas_jalan);
					$('#form-edit #id_klasifikasi').selectize()[0].selectize.setValue(respon.data.id_klasifikasi,false);
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