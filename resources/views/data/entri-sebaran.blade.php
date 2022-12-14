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
				   		{{Html::btnModal('<i class="la la-plus-circle"></i> Tambah','modal-tambah','primary')}}
				   		<hr>
				   		@endif
						<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
							<thead>
								<tr>
									<th width="1%">#</th>
									<th width="5%">Kode</th>
									<th width="25%">Ruas Jalan</th>
									<th width="15%">Desa</th>
									<th width="15%">Kecamatan</th>
									<th width="5%">Tahun</th>
									<th width="8%">Latitude</th>
									<th width="8%">Longitude</th>
									<th width="10%">Kondisi</th>
									<th width="8%">Actions</th>
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
<style type="text/css">
	.tooltip-inner {
	    max-width: 350px;
	    /* If max-width does not work, try using width instead */
	    width: 300px; 
	    text-align: left !important;
	}
	@foreach($ref_tingkat_kerusakan as $rtk)
	.bg-rusak-{{$rtk->kode_rusak}} {
	    --bs-bg-opacity: 1;
	    background-color: {{$rtk->warna}} !important;
	    color: white !important;
	}
	@endforeach
</style>
@if(Access::UserCanCreate())
<!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-tambah',url($main_path."/insert")) }}
	{{Html::mOpenLG80('modal-tambah','Tambah Titik Kerusakan Ruas Jalan')}}
		<div class="row">
			<div class="col-md-4">
				<div class="card">
				  <div class="card-body">
				  	<h5 class="card-subtitle mb-2 text-muted"><b>Data Atribut Titik Kerusakan</b></h5>
				  	<hr>
				    {{ Form::bsSelect2('Ruas Jalan','id_jalan',$list_ruas_jalan,'',true,'md-8')}}
					{{ Form::bsSelect2('Kecamatan','id_kecamatan',$list_kecamatan,'',true,'md-8')}}
					{{ Form::bsSelect2('Desa','id_desa',[],'',true,'md-8')}}
					{{ Form::bsSelect2('Tahun Survey','tahun',$list_tahun_survey,'',true,'md-8')}}
					{{ Form::bsSelect2('Tingkat Kerusakan','id_tingkat_kerusakan',$list_tingkat_kerusakan,'',true,'md-8')}}
				  </div>
				</div>
			</div>

			<div class="col-md-5">
				<div class="card">
				  <div class="card-body">
				  	<h5 class="card-subtitle mb-2 text-muted"><b>Titik Koordinat</b></h5>
				  	<hr>
				  	<div class="row">
				    	<div class="col-md-6">
				    		{{ Form::bsKoordinat('Latitude (Garis Lintang)','latitude','',true,'md-8')}}
				    	</div>
				    	<div class="col-md-6">
				    		{{ Form::bsKoordinat('Longitude (Garis Bujur)','longitude','',true,'md-8')}}
				    	</div>
					</div>
					<p>
						<button type="button" id="btn-peta" class="btn btn-outline btn-outline-primary">
						<i class="las la-map-marker-alt"></i> Lihat Titik Google Map</button>
						<a class="float-end" data-bs-toggle="tooltip" data-bs-html="true" data-bs-original-title="<small class='p-2'>
							<ul>
								<li>Geser Tanda Lokasi (Marker) untuk menyesuaian  titik lokasi agar lebih akurat sesuai google map.</li>
								<li>Scroll untuk perbesar / perkecil skala peta pada google maps</li>
							</ul>
						</small>"><i class="las la-info-circle"></i> Petunjuk</a>
					</p>
					{{ Form::bsTextAreaReadOnly('Geo Location','geo_location','',false,'md-8')}}
					<div id="map_canvas_tambah" class="map-canvas mb-1" style="width: 100%; height: 350px;"></div>
				  </div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card">
				  <div class="card-body">
				  	<h5 class="card-subtitle mb-2 text-muted"><b>Upload Gambar</b></h5>
				  	<hr>
				  	{{ Form::bsHidden('id_gambar','')}}
				  	<img style="cursor: pointer;" id="img-gambar" src="{{asset('img/placeholder.png')}}" 
				  		class="img-thumbnail img-fluid mt-1 image-view" alt="Gambar1">
				  	<div class="col mt-2">
				  		<div class="d-grid gap-2">
					  		<button id="btn-gambar" type="button" data-form="form-tambah" data-field="gambar"  data-img="img-gambar"
					  			class="btn btn-upload-gambar btn-outline btn-outline-primary"><i class="la la-upload"></i> Upload Gambar</button>
					  		<button type="button" data-form="form-tambah" data-field="gambar"  data-img="img-gambar"
					  			class="btn btn-delete-gambar btn-outline btn-outline-secondary"><i class="la la-trash"></i> Hapus Gambar</button>
					  	</div>
					  	<p class="mt-2"><small>Gambar yang disarankan adalah landscape dengan ukuran 1280 x 720 pixel</small></p>
				  	</div>
				  </div>
				</div>
				
			</div>
		</div>
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
@endif

@if(Access::UserCanUpdate())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-edit',url($main_path."/update")) }}
	{{Html::mOpenLG80('modal-edit','Ubah Data Kerusakan Ruas Jalan')}}
		<div class="row">
			<div class="col-md-4">
				<div class="card">
				  <div class="card-body">
				  	<h5 class="card-subtitle mb-2 text-muted"><b>Data Atribut Titik Kerusakan</b></h5>
				  	<hr>
				    {{ Form::bsReadOnly('Kode','kode','',true,'md-8')}}
				    {{ Form::bsSelect2('Ruas Jalan','id_jalan',$list_ruas_jalan,'',true,'md-8')}}
					{{ Form::bsSelect2('Kecamatan','id_kecamatan',$list_kecamatan,'',true,'md-8')}}
					{{ Form::bsSelect2('Desa','id_desa',[],'',true,'md-8')}}
					{{ Form::bsSelect2('Tahun Survey','tahun',$list_tahun_survey,'',true,'md-8')}}
					{{ Form::bsSelect2('Tingkat Kerusakan','id_tingkat_kerusakan',$list_tingkat_kerusakan,'',true,'md-8')}}
				  </div>
				</div>
			</div>

			<div class="col-md-5">
				<div class="card">
				  <div class="card-body">
				  	<h5 class="card-subtitle mb-2 text-muted"><b>Titik Koordinat</b></h5>
				  	<hr>
				  	<div class="row">
				    	<div class="col-md-6">
				    		{{ Form::bsKoordinat('Latitude (Garis Lintang)','latitude','',true,'md-8')}}
				    	</div>
				    	<div class="col-md-6">
				    		{{ Form::bsKoordinat('Longitude (Garis Bujur)','longitude','',true,'md-8')}}
				    	</div>
					</div>
					<p>
						<button type="button" id="btn-peta" class="btn btn-outline btn-outline-primary">
						<i class="las la-map-marker-alt"></i> Lihat Titik Google Map</button>
						<a class="float-end" data-bs-toggle="tooltip" data-bs-html="true" data-bs-original-title="<small class='p-2'>
							<ul>
								<li>Geser Tanda Lokasi (Marker) untuk menyesuaian  titik lokasi agar lebih akurat sesuai google map.</li>
								<li>Scroll untuk perbesar / perkecil skala peta pada google maps</li>
							</ul>
						</small>"><i class="las la-info-circle"></i> Petunjuk</a>
					</p>
					{{ Form::bsTextAreaReadOnly('Geo Location','geo_location','',false,'md-8')}}
					<div id="map_canvas_edit" class="map-canvas mb-1" style="width: 100%; height: 350px;"></div>
				  </div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card">
				  <div class="card-body">
				  	<h5 class="card-subtitle mb-2 text-muted"><b>Upload Gambar</b></h5>
				  	<hr>
				  	{{ Form::bsHidden('id_gambar','')}}
				  	<img style="cursor: pointer;" id="img-gambar" src="{{asset('img/placeholder.png')}}" 
				  		class="img-thumbnail img-fluid mt-1 image-view" alt="Gambar1">
				  	<div class="col mt-2">

				  		<div class="d-grid gap-2">
					  		<button id="btn-gambar" type="button" data-form="form-edit" data-field="gambar"  data-img="img-gambar"
					  			class="btn btn-upload-gambar btn-outline btn-outline-primary"><i class="la la-upload"></i> Upload Gambar</button>
					  		<button type="button" data-form="form-edit" data-field="gambar"  data-img="img-gambar"
					  			class="btn btn-delete-gambar btn-outline btn-outline-secondary"><i class="la la-trash"></i> Hapus Gambar</button>
					  	</div>
					  	<p class="mt-2"><small>Gambar yang disarankan adalah landscape dengan ukuran 1280 x 720 pixel</small></p>
				  	</div>
				  </div>
				</div>
				
			</div>
		</div>
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

@if(Access::UserCanCreate() || Access::UserCanUpdate())
<!-- MODAL FORM EDIT -->
{{ Form::bsOpen('form-upload-gambar',url($main_path."/upload-gambar")) }}
	 <input type="file" style="display: none;" id="upload-gambar" name="image" accept="image/*">
{{ Form::bsClose()}}
@endif



{{Html::mOpenLG80('modal-detil','Informasi Kerusakan Ruas Jalan')}}
	<div class="row" id="form-detil">
		
		<div class="col-md-4">
			<div class="card">
			  <div class="card-body">
			  	<h5 class="card-subtitle mb-2 text-muted"><b>Data Atribut Titik Kerusakan</b></h5>
			  	<div class="mb-1">
					<small><b>Kode</b></small><br>
					<span id="kode"></span>
				</div>
				<div class="mb-1">
					<small><b>Ruas Jalan</b></small><br>
					<span id="ruas_jalan"></span>
				</div>
				
				<div class="mb-1">
					<small><b>Desa</b></small><br>
					<span id="desa"></span>
				</div>
				<div class="mb-1">
					<small><b>Kecamatan</b></small><br>
					<span id="kecamatan"></span>
				</div>
				<div class="mb-1">
					<small><b>Kondisi</b></small><br>
					<span id="kondisi"></span>
				</div>
				<div class="mb-1">
					<small><b>Tahun Survey</b></small><br>
					<span id="tahun"></span>
				</div>
				<div class="mb-1">
					<small><b>Gambar</b></small><br>
					<img style="cursor: pointer;" id="img-gambar" src="{{asset('img/placeholder.png')}}" 
				  		class="img-thumbnail img-fluid mt-1 image-view" alt="gambar">
				</div>
			  </div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="card">
			  <div class="card-body">
			  	<div class="mb-1">
					<small><b>Koordinat (Latitude, Longitude)</b></small><br>
					<span id="koordinat"></span>
				</div>
				{{ Form::bsTextAreaReadOnly('Geo Location','geo_location','',false,'md-8')}}
				<div id="map_canvas_detil" class="map-canvas mb-1" style="width: 100%; height: 400px;"></div>
			  </div>
			</div>
		</div>

	</div>
{{Html::mCloseLG()}}


@endsection

@section("js")
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_API_KEY')}}"></script>
<script type="text/javascript">
	$(function(){
		$_default_latitude = {{ env('DEFAULT_LATITUDE') }};
		$_default_longitude = {{ env('DEFAULT_LONGITUDE') }};
		 

		var $tabel1 = $('#datatable').DataTable({
		    processing: true,
		    responsive: true,
		    fixedHeader: true,
		    serverSide: true,
		    ajax: "{{url($main_path.'/dt')}}",
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'kode' , name:"kode" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'nama_ruas_jalan' , name:"nama_ruas_jalan" , orderable:true, searchable: false,sClass:""},
		         {data:'nama_desa' , name:"nama_desa" , orderable:true, searchable: false,sClass:""},
		         {data:'nama_kecamatan' , name:"nama_kecamatan" , orderable:true, searchable: false,sClass:""},
		         {data:'tahun' , name:"tahun" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'latitude' , name:"latitude" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'longitude' , name:"longitude" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'nama_kerusakan' , name:"nama_kerusakan" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'action' , orderable:false, searchable: false,sClass:"text-center"},
		        ],
		        
		        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
	        	   	$('td:eq(8)', nRow).html('<span class="badge bg-rusak-'+aData['kode_rusak']+'">'+aData['nama_kerusakan']+'</span>');
		        	$(nRow).addClass( aData["rowClass"] );
	        	return nRow;
		    },
		    "drawCallback": function( settings ) {
		    	@if(Access::UserCanDelete())
		        initKonfirmDelete();
		        @endif
		        initDetilTitik();
		    }
		});

		function get_geo_location(latitude,longitude ){
        	var geocoder = new google.maps.Geocoder();
        	const latlng = {
			    lat: parseFloat(latitude),
			    lng: parseFloat(longitude),
			  };
			  geocoder.geocode({ location: latlng })
			    .then((response) => {
			      if (response.results[0]) {
			      	$("#form-tambah #geo_location").val(response.results[0].formatted_address);
			      	$("#form-edit #geo_location").val(response.results[0].formatted_address);
			      } else {
			        window.alert("No results found");
			      }
			    })
			    .catch((e) => window.alert("Geocoder failed due to: " + e));
        }

		@if(Access::UserCanCreate())

		function initialize_map_tambah() {
            // Creating map object
			
            var latitude = $("#form-tambah #latitude").val();
            var longitude = $("#form-tambah #longitude").val();
 

			if(latitude==0 && longitude==0|| latitude=='' && longitude==''){
				longitude = $_default_longitude;
				latitude = $_default_latitude;
			}
			 
            var map = new google.maps.Map(document.getElementById('map_canvas_tambah'), {
                zoom: 15,
                center: new google.maps.LatLng(latitude, longitude),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            // creates a draggable marker to the given coords
            var vMarker = new google.maps.Marker({
                position: new google.maps.LatLng(latitude, longitude),
                draggable: true
            });
            // adds a listener to the marker
            // gets the coords when drag event ends
            // then updates the input with the new coords
            get_geo_location(latitude,longitude);
            google.maps.event.addListener(vMarker, 'dragend', function (evt) {
                $("#form-tambah #latitude").val(evt.latLng.lat().toFixed(8));
                $("#form-tambah #longitude").val(evt.latLng.lng().toFixed(8));
                map.panTo(evt.latLng);
                get_geo_location(evt.latLng.lat().toFixed(8),evt.latLng.lng().toFixed(8))
            });
            // centers the map on markers coords
            map.setCenter(vMarker.position);
            // adds the marker on the map
            vMarker.setMap(map);
        }
        $("#form-tambah #btn-peta").on('click', function(){
        	_latitude = $("#form-tambah #latitude").val();
        	_longitude = $("#form-tambah #longitude").val();
        	if(_latitude==0.00 && _longitude==0.00){
        		_latitude = 0.81395;
				_longitude = 100.395723;
        	} 
			initialize_map_tambah();
        })

		var $select_desa_tambah = $('#form-tambah #id_desa').selectize({
			valueField: 'id',
			labelField: 'name',
			searchField: 'name',
			options: [],
			create: false
		});

	    var $select_desa_tambah_control = $select_desa_tambah[0].selectize;
	    $select_desa_tambah_control.clear();

	    var image_placeholder = "{{asset('img/placeholder.png')}}";
		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e){
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm();
			$('#form-tambah #id_jalan').selectize()[0].selectize.clear();
			$('#form-tambah #id_tingkat_kerusakan').selectize()[0].selectize.clear();
			$('#form-tambah #id_kecamatan').selectize()[0].selectize.clear();
			$("#form-tambah #img-gambar").attr('src', image_placeholder);
			$("#form-tambah #id_gambar").val(0)
			$("#form-tambah #map_canvas_tambah").html(null);
			$select_desa_tambah_control.clearOptions();
			$select_desa_tambah_control.clear();
			$('#form-tambah #tahun').selectize()[0].selectize.clear();
			enableButton("#form-tambah button[type=submit]");
			initialize_map_tambah();
		});


		$('#form-tambah #id_kecamatan').on('change', function(){
			_id_kecamatan = $(this).val();
			if(_id_kecamatan > 0){
				$.get("{{url($main_path.'/get-list-desa')}}/"+_id_kecamatan, function(respon){
					//console.log(respon);
					$select_desa_tambah_control.clearOptions();
					$select_desa_tambah_control.addOption(respon.data);
					$select_desa_tambah_control.clear();
				})
			}
		})

		$('#form-tambah').ajaxForm({
			beforeSubmit:function(){disableButton("#form-tambah button[type=submit]")},
			success:function($respon){
				if ($respon.status==true){
					 successNotify($respon.message);
					 $("#modal-tambah").modal('hide'); 
					 $tabel1.ajax.reload(null, true);
				}else{
					errorNotify($respon.message);
				}
				enableButton("#form-tambah button[type=submit]")
			},
			error:function($respon){
				$("#form-tambah button[type=submit]").button('reset');
				errorNotify($respon.message);
				enableButton("#form-tambah button[type=submit]")
			}
		}); 
		@endif

		@if(Access::UserCanUpdate())

		function initialize_map_edit() {
            // Creating map object
            const latitude = $("#form-edit #latitude").val();
            const longitude = $("#form-edit #longitude").val();
            var map = new google.maps.Map(document.getElementById('map_canvas_edit'), {
                zoom: 15,
                center: new google.maps.LatLng(latitude, longitude),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            // creates a draggable marker to the given coords
            var vMarker = new google.maps.Marker({
                position: new google.maps.LatLng(latitude, longitude),
                draggable: true
            });
            // adds a listener to the marker
            // gets the coords when drag event ends
            // then updates the input with the new coords
            get_geo_location(latitude,longitude);
            google.maps.event.addListener(vMarker, 'dragend', function (evt) {
                $("#form-edit #latitude").val(evt.latLng.lat().toFixed(8));
                $("#form-edit #longitude").val(evt.latLng.lng().toFixed(8));
                map.panTo(evt.latLng);
                get_geo_location(evt.latLng.lat().toFixed(8),evt.latLng.lng().toFixed(8))
            });
            // centers the map on markers coords
            map.setCenter(vMarker.position);
            // adds the marker on the map
            vMarker.setMap(map);
        }
        $("#form-edit #btn-peta").on('click', function(){
        	_latitude = $("#form-edit #latitude").val();
        	_longitude = $("#form-edit #longitude").val();
        	if(_latitude!=0 && _longitude!=0){
        		initialize_map_edit();
        	}
        })

        var $select_desa_edit = $('#form-edit #id_desa').selectize({
			valueField: 'id',
			labelField: 'name',
			searchField: 'name',
			options: [],
			create: false
		});

	    var $select_desa_edit_control = $select_desa_edit[0].selectize;
	    $select_desa_edit_control.clear();
	    $default_id_desa = 0;
		$validator_form_edit = $("#form-edit").validate();
		$("#modal-edit").on('show.bs.modal', function(e){
			$uuid  = $(e.relatedTarget).data('uuid');
			$validator_form_edit.resetForm();
			$("#form-edit").clearForm();
			$('#form-edit #id_jalan').selectize()[0].selectize.clear();
			$('#form-edit #id_tingkat_kerusakan').selectize()[0].selectize.clear();
			$('#form-edit #id_kecamatan').selectize()[0].selectize.clear();
			$("#form-edit #img-gambar").attr('src', image_placeholder);
			$("#form-edit #id_gambar").val(0)
			$("#form-edit #map_canvas_tambah").html(null);
			$select_desa_edit_control.clearOptions();
			$select_desa_edit_control.clear();
			$default_id_desa = 0;
			$('#form-edit #tahun').selectize()[0].selectize.clear();
			disableButton("#form-edit button[type=submit]")
			$('#form-edit #id_jalan').selectize()[0].selectize.clear();
			$.get("{{url($main_path.'/get-data-detil')}}/"+$uuid, function(respon){
				if(respon.status){
					$('#form-edit #uuid').val(respon.data.uuid);
					$('#form-edit #kode').val(respon.data.kode);
					$("#form-edit #img-gambar").attr('src', respon.gambar);
					$('#form-edit #id_jalan').selectize()[0].selectize.setValue(respon.data.id_jalan,false);
					$('#form-edit #id_tingkat_kerusakan').selectize()[0].selectize.setValue(respon.data.id_tingkat_kerusakan,false);
					$('#form-edit #tahun').selectize()[0].selectize.setValue(respon.data.tahun,false);
					$default_id_desa = respon.data.id_desa;
					$('#form-edit #id_kecamatan').selectize()[0].selectize.setValue(respon.data.id_kecamatan,false);
					$('#form-edit #id_gambar').val(respon.data.id_gambar);
					$('#form-edit #latitude').val(respon.data.latitude);
					$('#form-edit #longitude').val(respon.data.longitude);
					$('#form-edit #geo_location').val(respon.data.geo_location);
					initialize_map_edit();
					enableButton("#form-edit button[type=submit]");
				}else{
					errorNotify(respon.message);
				}
			})
		});

		$("#form-edit #id_kecamatan").on('change', function(){
			$val = $("#form-edit #id_kecamatan").val();
			if($val!==""){
				$.get("{{url($main_path.'/get-list-desa')}}/"+$val, function(respon){
					$select_desa_edit_control.clearOptions();
					$select_desa_edit_control.addOption(respon.data);
					$select_desa_edit_control.clear();
					if($default_id_desa > 0){
						$select_desa_edit_control.setValue($default_id_desa,false);
						$default_id_desa = 0;
					}
				}) 
			}
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
				errorNotify('Terjadi Kesalahan!');
			}
		}); 
		@endif


		@if(Access::UserCanCreate() || Access::UserCanUpdate())
			var $field_gambar = "";
			var $form_gambar = "";
			var $view_gambar = "";

			$(".btn-upload-gambar").on('click', function(){
			 	$field_gambar = $(this).data('field');
			 	$form_gambar = $(this).data('form');
			 	$("#form-upload-gambar #upload-gambar").trigger('click');
			 });

			$(".btn-delete-gambar").on('click', function(){
				$field_gambar = $(this).data('field');
			 	$form_gambar = $(this).data('form');
			 	$("#"+$form_gambar +" #img-"+$field_gambar).attr('src',image_placeholder);
				$("#"+$form_gambar +" #id_gambar").val('');
			 });

			$("#upload-gambar").on('change', function(){
		 		if($(this).val()){
		 			$("#form-upload-gambar").submit();
		 		}
			});

		 	$('#form-upload-gambar').ajaxForm({
				beforeSubmit:function(){ disableButton("#"+$form_gambar +" #btn-"+$field_gambar) },
				success:function($respon){
					enableButton("#"+$form_gambar +" #btn-"+$field_gambar);
					if ($respon.status==true){
						  $("#upload-gambar").val('');
						  $("#"+$form_gambar +" #img-"+$field_gambar).attr('src',$respon.image);
						  $("#"+$form_gambar +" #id_"+$field_gambar).val($respon.id_gambar);
					}else{
						$("#"+$form_gambar +" #img-"+$field_gambar).attr('src',image_placeholder);
						$("#"+$form_gambar +" #id_"+$field_gambar).val('');
						errorNotify($respon.message);
					}
				},
				error:function(){
					enableButton("#"+$form_gambar +" #btn-"+$field_gambar);
					$("#"+$form_gambar +" #img-"+$field_gambar).attr('src',image_placeholder);
					$("#"+$form_gambar +" #"+$field_gambar).val('');
					errorNotify('Gagal Upload Gambar!');
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

		$(".image-view").on('click', function(){
			$src = $(this).attr('src');
			if($src!=image_placeholder){
				$.alert({
				    title: '',
				    columnClass: 'col-md-9',
				    content: '<div width="100%" class="col-md-12"><center><img  width="100%" src="'+$src+'" class="img-thumbnail img-responseive img-fluid rounded-lg" ></center></div>',
				});
			}
		});

		initDetilTitik = function(){
			$(".view-detil").on('click', function(){
				$uuid = $(this).data('uuid');
				$.get("{{url($main_path.'/get-data-detil')}}/"+$uuid, function(respon){
					if(respon.status){
						 console.log(respon);
						 $("#form-detil #kode").html(respon.kode);
						 $("#form-detil #koordinat").html(respon.data.latitude + ', '+respon.data.longitude);
						 $("#form-detil #ruas_jalan").html(respon.jalan.kode_jalan + ' '+respon.jalan.nama_ruas_jalan + ' ('+ respon.klasifikasi_jalan.nama_klasifikasi+')');
						 $("#form-detil #kecamatan").html(respon.kecamatan.kode_kecamatan + '. '+respon.kecamatan.nama_kecamatan);
						 $("#form-detil #desa").html(respon.desa.kode_desa + '. '+respon.desa.nama_desa);
						 $("#form-detil #kondisi").html('<span class="badge bg-rusak-'+respon.tingkat_kerusakan.kode_rusak+'">'+ respon.tingkat_kerusakan.nama_kerusakan+'</span>');
						  $("#form-detil #tahun").html(respon.data.tahun);
						  $("#form-detil #img-gambar").attr('src',respon.gambar);
						  $("#form-detil #geo_location").val(respon.data.geo_location);
						 $("#modal-detil").modal('show');
						 initialize_map_detil(respon.data.latitude, respon.data.longitude);
					}else{
						errorNotify(respon.message);
					}
				})
			});
		}

		function initialize_map_detil(latitude, longitude) {
            var map = new google.maps.Map(document.getElementById('map_canvas_detil'), {
                zoom: 15,
                center: new google.maps.LatLng(latitude, longitude),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            // creates a draggable marker to the given coords
            var vMarker = new google.maps.Marker({
                position: new google.maps.LatLng(latitude, longitude),
                animation:google.maps.Animation.BOUNCE,
            });
            map.setCenter(vMarker.position);
            // adds the marker on the map
            vMarker.setMap(map);
        }
		
			 

	})
</script>
@endsection