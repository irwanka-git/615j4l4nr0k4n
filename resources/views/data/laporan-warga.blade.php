<?php
$main_path = Request::segment(1);
use App\Library\Access;
?>
@extends('layout')
@section("pagetitle")
	{{$pagetitle}}
@endsection

@section('content')
		<style type="text/css">
		.tooltip-inner {
		    max-width: 350px;
		    /* If max-width does not work, try using width instead */
		    width: 300px; 
		    text-align: left !important;
		}
		.badge-circle{
			border-radius: 50% !important;
			vertical-align: middle;
			width: 20px;
 		    height: 20px;
 		    padding: 0.45em !important;
		}

		.bg-valid{
			background-color: {{env("FILLCOLORLAPORANVALID")}};
		}

		.bg-baru{
			background-color: {{env("FILLCOLORLAPORAN")}};
		}
		 
	</style>
		<div class="row" id="map-panel">
			<div class="col-12">
				<div class="card">
					<div class="card-body mb-1">
						<div class="row">
								<div class="col-md-12">
									<h4>Laporan Kondisi Jalan</h4>
									
									<hr>
									<ul class="nav nav-tabs" id="myTab" role="tablist">
									  <li class="nav-item" role="presentation">
									    <button class="nav-link active" id="map-tab" data-bs-toggle="tab" data-bs-target="#map-tab-pane" type="button" role="tab" aria-controls="map-tab-pane" aria-selected="true">Laporan Titik Kerusakan</button>
									  </li>
									  <li class="nav-item" role="presentation">
									    <button class="nav-link" id="daftar-tab" data-bs-toggle="tab" data-bs-target="#daftar-tab-pane" type="button" role="tab" aria-controls="daftar-tab-pane" aria-selected="false">Laporan Warga</button>
									  </li>
									</ul>
									<div class="tab-content" id="myTabContent">
										  <div class="tab-pane fade show active" 
										  	id="map-tab-pane" role="tabpanel" aria-labelledby="map-tab" tabindex="0">
										  		<div class="p-2 pt-4">
										  			<label class="pr-5">Layer Peta: </label> &nbsp;&nbsp;&nbsp;
													<div class="form-switch mr-3" style="display:inline !important;">
														<input class="form-check-input check-layer" value="visible" type="checkbox" name="_visbility_administrasi" id="_visbility_administratif">
														<label class="form-check-label" for="flexSwitchCheckDefault">Administratif</label>
													</div>&nbsp;&nbsp;&nbsp;
													<div class="form-switch mr-3" style="display:inline !important;">
														<input class="form-check-input check-layer" value="visible" type="checkbox" name="_visbility_landscape" id="_visbility_landscape">
														<label class="form-check-label" for="flexSwitchCheckDefault">Landscape</label>
													</div>&nbsp;&nbsp;&nbsp;
													<div class="form-switch mr-3" style="display:inline !important;">
														<input class="form-check-input check-layer" value="visible" type="checkbox" name="_visbility_poi" id="_visbility_poi">
														<label class="form-check-label" for="flexSwitchCheckDefault">Objek Vital</label>
													</div>
													<div class="float-end">
														<span class="badge badge-circle bg-valid">L</span> Laporan Warga 
														&nbsp;&nbsp;&nbsp;
													</div>
													<div id="map_canvas_detil" class="map-canvas mt-3 mb-1" style="width: 100%; height: 600px;"></div>
													
										  		</div>
										  </div>
										  <div class="tab-pane fade" 
										  	id="daftar-tab-pane" role="tabpanel" aria-labelledby="daftar-tab" tabindex="0">
										  		<div class="p-2 pt-4">
										  			<?php 
										  				$list_status_laporan = [
											  				array(
											  					"text"=>"Sudah diverifikasi",
											  					"value"=>1
											  				),
											  				array(
											  					"text"=>"Belum diverifikasi",
											  					"value"=>0
											  				)
											  			];

										  				$list_status_laporan = json_decode(json_encode($list_status_laporan));
										  			?>
										  			{{ Form::bsSelect2('Status Laporan','status_laporan',$list_status_laporan,0,true,'md-8')}}
										  			<hr>
										  			<table id="datatable" class="table table-striped table-hover table-sm" style="width:100%">
														<thead>
															<tr>
																<th width="1%">#</th>
																<th width="5%">Kode</th>
																<th width="25%">Geo Location</th>
																<th width="15%">Koordinat</th>
																<th width="10%">Pelapor</th>
																<th width="15%">Isi Laporan</th>
																<th width="10%">Waktu</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
										  		</div>
										  </div>
									</div>
								</div>
						</div>
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
</style>

{{Html::mOpenLG80('modal-detil','Laporan Kerusakan Ruas Jalan')}}
	<div class="row" id="form-detil">
		<div class="col-md-8">
			<div class="card">
			  <div class="card-body">
			  	<div class="mb-1">
					<small><b>Koordinat (Latitude, Longitude)</b></small><br>
					<span id="koordinat"></span>
				</div>
				{{ Form::bsTextAreaReadOnly('Geo Location','geo_location','',false,'md-8')}}
				<div id="map_canvas_detil_view" class="map-canvas mb-1" style="width: 100%; height: 400px;"></div>
				<div id="panel-verifikasi">
					<hr>
					{{ Form::bsTextArea('Isi Tanggapan Laporan:','respon_verifikasi','',false,'md-8')}}
					<hr>
					<p>
						<button id="btn-verifikasi" class="btn btn-primary"><i class="fa fa-check"></i> Verifikasi</button>
						<button id="btn-tolak" class="btn btn-danger"> <i class="fa fa-ban"></i> Tolak Laporan</button>
					</p>
				</div>
				<div id="panel-update">
					<hr>
					{{ Form::bsTextArea('Isi Tanggapan Laporan:','respon_update','',false,'md-8')}}
					<hr>
					<p>
						<button id="btn-update" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
						<button id="btn-delete" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus Laporan</button>
					</p>
				</div>
			  </div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="card">
			  <div class="card-body">
			  	<h5 class="card-subtitle mb-2 text-muted"><b>Data Laporan</b></h5>
			  	<div class="mb-1">
					{{ Form::bsReadOnly('Nomor Laporan','kode','',false,'md-8')}}
				</div>				
				<div class="mb-1">
					{{ Form::bsReadOnly('Nama Pelapor','nama_pelapor','',false,'md-8')}}
				</div>
				<div class="mb-1">
					{{ Form::bsReadOnly('Alamat Pelapor','alamat_pelapor','',false,'md-8')}}
				</div>
				<div class="mb-1">
					{{ Form::bsReadOnly('No HP Pelapor','no_hp_pelapor','',false,'md-8')}}
				</div>
				<div class="mb-1">
					{{ Form::bsTextAreaReadOnly('Isi Laporan','isi_laporan','',false,'md-8')}}
				</div>
				<div class="mb-1">
					<small><b>Gambar</b></small><br>
					<img style="cursor: pointer;" id="img-gambar" src="{{asset('img/placeholder.png')}}" 
				  		class="img-thumbnail img-fluid mt-1 image-view" alt="gambar">
				</div>
				{{ Form::bsHidden('uuid_laporan','')}}
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
		 
		 var map = null;		
		var map_height = $("body").height() - $("#map-panel").position().top - 200;
        $("#map_canvas_detil").css('height',map_height+'px');
        
        $("#_visbility_administratif").prop('checked', true);
        $("#_visbility_landscape").prop('checked', true);
        $("#_visbility_poi").prop('checked', false);

        function update_custom_element(){
        	_visibility_poi = $("#_visbility_poi").prop('checked') ? "visible":"off";
        	_visbility_administratif = $("#_visbility_administratif").prop('checked') ? "visible":"off";
        	_visbility_landscape = $("#_visbility_landscape").prop('checked') ? "visible":"off";
        	var customStyled = [
				{featureType: "administrative",elementType: "marker",stylers: [{ visibility: _visbility_administratif },]},
				{featureType: "landscape",elementType: "marker",stylers: [{ visibility: _visbility_landscape },]},
				{featureType: "poi",elementType: "marker",stylers: [{ visibility: _visibility_poi },]},
			];
			map.set('styles',customStyled);
        }

        $(".check-layer").on('change', function(){
        	update_custom_element();
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

		function generate_map(centroid, points, bound) {
             map = new google.maps.Map(document.getElementById('map_canvas_detil'), {
                zoom: 12,
                center: new google.maps.LatLng(centroid.latitude, centroid.longitude),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            update_custom_element();
            var infowindow = new google.maps.InfoWindow({maxWidth: 300});
    		var marker, i;
    		let url = "http://maps.google.com/mapfiles/ms/icons/";
            for (i = 0; i < points.length; i++) {  
			      marker = new google.maps.Marker({
			      	label: {text: "L", color: 'white', fontSize: "9px"},
			        position: new google.maps.LatLng(points[i].latitude, points[i].longitude),
			        map: map,
			        icon: {
				        path: google.maps.SymbolPath.CIRCLE,
				        //size: new google.maps.Size(38, 38),
				        // scaledSize: new google.maps.Size(32, 32),
				        // labelOrigin: new google.maps.Point(10, 10),
				        fillColor: "{{env('FILLCOLORLAPORANVALID')}}",
				        fillOpacity: 0.9,
				        strokeColor: "{{env('STROKECOLORLAPORANVALID')}}",
				        strokeOpacity: 1,
				        strokeWeight: 1,
				        scale: 8
				    },
				    
			      });
			      google.maps.event.addListener(marker, 'click', (function(marker, i) {
			        return function() {
			          $.get("{{url($main_path.'/get-info-window')}}/"+points[i].uuid, function(respon){
			          		infowindow.setContent(respon.informasi);
			          		infowindow.open(map, marker);
			          });
			        }
		      })(marker, i));
		    }
			
			//console.log(bound);
			var bounds = new google.maps.LatLngBounds();
			bounds.extend(new google.maps.LatLng(bound.min_latitude, bound.min_longitude));
			bounds.extend(new google.maps.LatLng(bound.max_latitude, bound.max_longitude));
			map.fitBounds(bounds);
			
        }
         
        initmapdefault = function(){
        	$.get("{{url('get-data-map-default-laporan')}}", function(respon){
        	 	centroid = respon.centroid
        		generate_map(centroid, respon.points, respon.bound);
        		update_custom_element();
        	});
        }
        //alert(3);
        initmapdefault();
        initialize_map_tambah();

        function initialize_map_tambah() {
            // Creating map object
            var latitude = $("#form-tambah #latitude").val();
            var longitude = $("#form-tambah #longitude").val();

            if(!latitude || !longitude){
            	latitude = {{ env('DEFAULT_LATITUDE') }};
            	longitude = {{ env('DEFAULT_LONGITUDE') }};
            	$("#form-tambah #latitude").val(latitude);
            	$("#form-tambah #longitude").val(longitude);
            	set_marker(latitude, longitude);
            }else{
            	set_marker(latitude, longitude);
            }            
        }

        function set_marker(latitude, longitude){

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

         

	})
</script>


<script>
	$(function(){
		$status = $("#status_laporan").val();
		$base_path_dt = "{{url($main_path.'/dt')}}";
		$path_dt = $base_path_dt + "?status=" + $status
		var $tabel1 = $('#datatable').DataTable({
		    processing: true,
		    responsive: true,
		    fixedHeader: true,
		    serverSide: true,
		    ajax: $path_dt,
		    "iDisplayLength": 25,
		    columns: [
		    	 {data:'DT_Row_Index' , orderable:false, searchable: false,sClass:""},
		         {data:'kode' , name:"kode" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'geo_location' , name:"geo_location" , orderable:true, searchable: false,sClass:""},
		         {data:'koordinat' , name:"koordinat" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'nama_pelapor' , name:"nama_pelapor" , orderable:true, searchable: false,sClass:"text-center"},
		         {data:'isi_laporan' , name:"tahun" , orderable:true, searchable: false,sClass:""},
		         {data:'created_at' , name:"created_at" , orderable:true, searchable: false,sClass:"text-center"},
		        ],
		        
		    "drawCallback": function( settings ) {
		        initDetilTitik();
		    }
		});

		initDetilTitik = function(){
			$(".view-detil").on('click', function(){
				$uuid = $(this).data('uuid');
				$.get("{{url($main_path.'/get-data-detil')}}/"+$uuid, function(respon){
					if(respon.status){
						 console.log(respon);
						 $("#form-detil #kode").val(respon.data.kode);
						 $("#form-detil #koordinat").html(respon.data.latitude + ', '+respon.data.longitude);
						 $("#form-detil #nama_pelapor").val(respon.data.nama_pelapor);
						 $("#form-detil #alamat_pelapor").val(respon.data.alamat_pelapor);
						 $("#form-detil #no_hp_pelapor").val(respon.data.no_hp_pelapor);
						 $("#form-detil #isi_laporan").val(respon.data.isi_laporan);
						 $("#form-detil #img-gambar").attr('src',respon.gambar);
						 $("#form-detil #geo_location").val(respon.data.geo_location);
						 $("#respon_verifikasi").val(respon.data.respon_laporan)
						 $("#respon_update").val(respon.data.respon_laporan)
						 if (respon.data.valid ==1){
						 	$("#panel-update").show()
						 	$("#panel-verifikasi").hide()
						 }else{
						 	$("#panel-update").hide()
						 	$("#panel-verifikasi").show()
						 }
						 $("#uuid_laporan").val(respon.data.uuid);
						 $("#modal-detil").modal('show');
						 initialize_map_detil(respon.data.latitude, respon.data.longitude);
					}else{
						errorNotify(respon.message);
					}
				})
			});
		}

		function initialize_map_detil(latitude, longitude) {
            var map = new google.maps.Map(document.getElementById('map_canvas_detil_view'), {
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
		

		$('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
	        // https://datatables.net/reference/api/columns.adjust() states that this function is trigger on window resize
	        $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
	    });

		$("#status_laporan").on('change', function(){
			$status = $("#status_laporan").val();
			$path_dt = $base_path_dt + "?status=" + $status
			$tabel1.ajax.url($path_dt).load();	
		})

		$("#btn-verifikasi").on('click', function(){
				$.confirm({
				    title: 'Anda Yakin Terima (Verifikasi) Laporan Ini?',
				    content: "",
				    buttons: {
				        
				        cancel :{
				        	text: 'Batalkan'
				        },
				        confirm: {
				        	text: 'Ya, Verifikasi',
				        	btnClass: 'btn-primary',
				        	action:function(){
				        		$("#modal-detil").modal('hide');
				        		data = {
				        			"_token":"{{csrf_token()}}",
				        			"respon": $("#respon_verifikasi").val(), 
				        			"uuid": $("#uuid_laporan").val()
				        		}
				        		$.post("{{$main_path}}/submit-verifikasi", data).done(function($respon){
				        			successNotify($respon.message);
					 				$tabel1.ajax.reload(null, true);
				        		});
				        	}
				        },
				    }
				});
		})

		$("#btn-update").on('click', function(){
				$.confirm({
				    title: 'Anda Yakin ingin Update Tanggapan Laporan Ini?',
				    content: "",
				    buttons: {
				        
				        cancel :{
				        	text: 'Batalkan'
				        },
				        confirm: {
				        	text: 'Ya, Update',
				        	btnClass: 'btn-primary',
				        	action:function(){
				        		$("#modal-detil").modal('hide');
				        		data = {
				        			"_token":"{{csrf_token()}}",
				        			"respon": $("#respon_update").val(), 
				        			"uuid": $("#uuid_laporan").val()
				        		}
				        		$.post("{{$main_path}}/submit-verifikasi", data).done(function($respon){
				        			successNotify($respon.message);
					 				$tabel1.ajax.reload(null, true);
				        		});
				        	}
				        },
				    }
				});
		})

		$("#btn-tolak").on('click', function(){
				$.confirm({
				    title: 'Anda Yakin Ingin Tolak Laporan Ini?',
				    content: "",
				    buttons: {
				        
				        cancel :{
				        	text: 'Batalkan'
				        },
				        confirm: {
				        	text: 'Ya, Tolak',
				        	btnClass: 'btn-danger',
				        	action:function(){
				        		$("#modal-detil").modal('hide');
				        		data = {
				        			"_token":"{{csrf_token()}}", 
				        			"uuid": $("#uuid_laporan").val()
				        		}
				        		$.post("{{$main_path}}/submit-tolak", data).done(function($respon){
				        			successNotify($respon.message);
					 				$tabel1.ajax.reload(null, true);
				        		});
				        	}
				        },
				    }
				});
		})

		$("#btn-delete").on('click', function(){
				$.confirm({
				    title: 'Anda Yakin Ingin Hapus Laporan Ini?',
				    content: "",
				    buttons: {
				        
				        cancel :{
				        	text: 'Batalkan'
				        },
				        confirm: {
				        	text: 'Ya, Hapus',
				        	btnClass: 'btn-danger',
				        	action:function(){
				        		$("#modal-detil").modal('hide');
				        		data = {
				        			"_token":"{{csrf_token()}}", 
				        			"uuid": $("#uuid_laporan").val()
				        		}
				        		$.post("{{$main_path}}/submit-tolak", data).done(function($respon){
				        			successNotify($respon.message);
					 				$tabel1.ajax.reload(null, true);
				        		});
				        	}
				        },
				    }
				});
		})

	})
</script>
@endsection

	