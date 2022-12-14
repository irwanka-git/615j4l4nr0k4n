<?php
$main_path = Request::segment(1);
use App\Library\Access;
?>
@extends('layout-public')
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
									{{Html::btnModal('<i class="la la-plus-circle"></i> Laporan Baru','modal-tambah','primary')}}
									<hr>
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
					</div>
				</div>
			</div>
		</div>

		<div id=""
 
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
 
 {{ Form::bsOpen('form-upload-gambar',url($main_path."/upload-gambar")) }}
	 <input type="file" style="display: none;" id="upload-gambar" name="image" accept="image/*">
{{ Form::bsClose()}}

 {{ Form::bsOpen('form-tambah',url($main_path."/insert")) }}
	{{Html::mOpenLG80('modal-tambah','Input Laporan Kondisi Jalan')}}
		<div class="row">
			<div class="col-md-8">
				<div class="card">
				  <div class="card-body">
				  	<h5 class="card-subtitle mb-2 text-muted"><b>Input Titik Koordinat</b></h5>
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
					<div class="alert alert-primary">
						<div class=" alert-message">
							<p><b>Geser Tanda Lokasi (Marker) untuk menyesuaian  titik lokasi agar lebih akurat sesuai google map</b></p>
						</div>
					</div>
					{{ Form::bsTextAreaReadOnly('Geo Location','geo_location','',true,'md-8')}}
					<div id="map_canvas_tambah" class="map-canvas mb-1" style="width: 100%; height: 550px;"></div>
					<hr>
				  </div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card">
				  <div class="card-body">
				  	<h5 class="card-subtitle mb-2 text-muted"><b>Laporan</b></h5>
				  	<hr>
				  	{{ Form::bsTextField('Nama Pelapor','nama_pelapor','',true,'md-8') }}
				  	{{ Form::bsTextField('Alamat Pelapor','alamat_pelapor','',true,'md-8') }}
				  	{{ Form::bsTextField('No HP Pelapor','no_hp_pelapor','',true,'md-8') }}
				  	{{ Form::bsTextArea('Isi laporan','isi_laporan','',true,'md-12')}}
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
			          $.get("{{url('laporan-kerusakan-public/get-info-window')}}/"+points[i].uuid, function(respon){
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

        $("#form-tambah #btn-peta").on('click', function(){
	        	_latitude = $("#form-tambah #latitude").val();
	        	_longitude = $("#form-tambah #longitude").val();
	        	if(_latitude!=0 && _longitude!=0){
	        		initialize_map_tambah();
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

        var image_placeholder = "{{asset('img/placeholder.png')}}";
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

	})
</script>
@endsection

	