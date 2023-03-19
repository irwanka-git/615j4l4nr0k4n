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
		@foreach($ref_tingkat_kerusakan as $rtk)
		.bg-rusak-{{$rtk->kode_rusak}} {
		    --bs-bg-opacity: 1;
		    background-color: {{$rtk->warna}} !important;
		    color: white !important;
		}
		@endforeach
	</style>
		<div class="row" id="map-panel">
			<div class="col-12">
				<div class="card">
					<div class="card-body mb-1">
						<div class="row">
								<div class="col-md-3">
									<h5 class="card-title mb-3"><b>Sebaran Titik Kerusakan</b></h5>
									<div class="card">
										<div class="card-body mb-1">
											<label>Filter Data</label>
											<hr>
											{{ Form::bsOpen('form-cari',"#") }}
												{{ Form::bsSelect2_all('Kecamatan','id_kecamatan',$list_kecamatan,'',true,'md-8')}}
												{{ Form::bsSelect2_all('Tahun Survey','tahun',$list_tahun_survey,'',true,'md-8')}}
												{{ Form::bsSelect2_all('Tingkat Kerusakan','id_tingkat_kerusakan',$list_tingkat_kerusakan,'',true,'md-8')}}
												<div class="d-grid gap-2">
													<button type="button" id="btn-cari" class="btn btn-primary"><i class="la la-search"></i> Cari</button>
												</div>
											{{ Form::bsClose()}}
										</div>
									</div>
								</div>
								<div class="col-md-9">
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
										@foreach($ref_tingkat_kerusakan as $rtk)
										<span class="badge badge-circle bg-rusak-{{$rtk->kode_rusak}}">{{$rtk->kode_rusak}}</span>&nbsp;{{$rtk->nama_kerusakan}}&nbsp;&nbsp;
										@endforeach
									</div>
									<div id="map_canvas_detil" class="map-canvas mt-3 mb-1" style="width: 100%; height: 500px;"></div>
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
	@foreach($ref_tingkat_kerusakan as $rtk)
	.bg-rusak-{{$rtk->kode_rusak}} {
	    --bs-bg-opacity: 1;
	    background-color: {{$rtk->warna}} !important;
	    color: white !important;
	}
	@endforeach
</style>
 
@endsection

@section("js")
<style type="text/css">
	 .custom-clustericon {
        background: var(--cluster-color);
        color: #fff;
        border-radius: 100%;
        font-weight: bold;
        font-size: 15px;
        display: flex;
        align-items: center;
      }

      .custom-clustericon::before,
      .custom-clustericon::after {
        content: "";
        display: block;
        position: absolute;
        width: 100%;
        height: 100%;

        transform: translate(-50%, -50%);
        top: 50%;
        left: 50%;
        background: var(--cluster-color);
        opacity: 0.2;
        border-radius: 100%;
      }

      .custom-clustericon::before {
        padding: 7px;
      }

      .custom-clustericon::after {
        padding: 14px;
      }

      .custom-clustericon-1 {
        --cluster-color: #9E9E9E;
      }

      .custom-clustericon-2 {
        --cluster-color: #FCA100;
      }

      .custom-clustericon-3 {
        --cluster-color: #F42A06;
      }

</style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_API_KEY')}}"></script>

<script src="https://googlemaps.github.io/js-markerclustererplus/dist/index.min.js"></script>

<script type="text/javascript">
	$(function(){
		$("#form-cari #id_kecamatan").selectize()[0].selectize.clear();
		$("#form-cari #tahun").selectize()[0].selectize.clear();
		$('#form-cari #id_tingkat_kerusakan').selectize()[0].selectize.clear();
		$('#form-cari #id_kecamatan').selectize()[0].selectize.setValue('_all',false);
		$('#form-cari #tahun').selectize()[0].selectize.setValue('_all',false);
		$('#form-cari #id_tingkat_kerusakan').selectize()[0].selectize.setValue('_all',false);

		var map = null;		
		var map_height = $("body").height() - $("#map-panel").position().top - 100;
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

		function generate_map(centroid, points, bound) {
             map = new google.maps.Map(document.getElementById('map_canvas_detil'), {
                zoom: 8,
                center: new google.maps.LatLng(centroid.latitude, centroid.longitude),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            update_custom_element();
            var infowindow = new google.maps.InfoWindow({maxWidth: 300});
    		var marker, i;


    		//variabel cluster
    		var marker_cluster_ringan =[] ;
    		var marker_cluster_sedang =[] ;
    		var marker_cluster_berat =[] ;

    		var style_cluster_ringan = [
	          {
	            width: 30,
	            height: 30,
	            className: "custom-clustericon-1",
	          },
	          {
	            width: 35,
	            height: 35,
	            className: "custom-clustericon-1",
	          },
	          {
	            width: 40,
	            height: 40,
	            className: "custom-clustericon-1",
	          },
	        ];

		  var style_cluster_sedang = [
	          {
	            width: 30,
	            height: 30,
	            className: "custom-clustericon-2",
	          },
	          {
	            width: 35,
	            height: 35,
	            className: "custom-clustericon-2",
	          },
	          {
	            width: 40,
	            height: 40,
	            className: "custom-clustericon-2",
	          },
	        ];

		  var style_cluster_berat = [
	          {
	            width: 30,
	            height: 30,
	            className: "custom-clustericon-3",
	          },
	          {
	            width: 35,
	            height: 35,
	            className: "custom-clustericon-3",
	          },
	          {
	            width: 40,
	            height: 40,
	            className: "custom-clustericon-3",
	          },
	        ];
		  //end variabel cluster


    		let url = "http://maps.google.com/mapfiles/ms/icons/";
            for (i = 0; i < points.length; i++) {  
			      marker = new google.maps.Marker({
			      	label: {text: points[i].kode_rusak, color: 'white', fontSize: "9px"},
			        position: new google.maps.LatLng(points[i].latitude, points[i].longitude),
			        map: map,
			        icon: {
				        path: google.maps.SymbolPath.CIRCLE,
				        //size: new google.maps.Size(38, 38),
				        // scaledSize: new google.maps.Size(32, 32),
				        // labelOrigin: new google.maps.Point(10, 10),
				        fillColor: points[i].warna,
				        fillOpacity: 0.9,
				        strokeColor: points[i].warna_stroke,
				        strokeOpacity: 1,
				        strokeWeight: 1,
				        scale: 8
				    },
				    
			      });
			      google.maps.event.addListener(marker, 'click', (function(marker, i) {
			        return function() {
			          $.get("{{url('sebaran-kerusakan/get-info-window')}}/"+points[i].uuid, function(respon){
			          		infowindow.setContent(respon.informasi);
			          		infowindow.open(map, marker);
			          });
			        }
		      })(marker, i));


			      //marker kelompok kerusakan
			   if(points[i].kode_rusak=="R"){
			   	marker_cluster_ringan.push(marker);
			   }

			   if(points[i].kode_rusak=="S"){
			   	marker_cluster_sedang.push(marker);
			   }

			    if(points[i].kode_rusak=="B"){
			   	marker_cluster_berat.push(marker);
			   }
			   //end markter kelompok kerusakan

		    }
			
			//console.log(bound);
			var bounds = new google.maps.LatLngBounds();
			bounds.extend(new google.maps.LatLng(bound.min_latitude, bound.min_longitude));
			bounds.extend(new google.maps.LatLng(bound.max_latitude, bound.max_longitude));
			//map.fitBounds(bounds);

			//buat cluster marker
			clusterRingan = new MarkerClusterer(
					 map, 
					 marker_cluster_ringan, 
					  {
		          	styles: style_cluster_ringan,
		          	gridSize: 40,
		          	clusterClass:  "custom-clustericon",
		         	}
					);
        

        clusterSedang = new MarkerClusterer(
					 map, 
					 marker_cluster_sedang, 
					  {
		          	styles: style_cluster_sedang,
		          	gridSize: 40,
		          	clusterClass:  "custom-clustericon",
		         	}
					);
        

        clusterBerat = new MarkerClusterer(
					 map, 
					 marker_cluster_berat, 
					  {
		          	styles: style_cluster_berat,
		          	gridSize: 40,
		          	clusterClass:  "custom-clustericon",
		         	}
					);

        //end buat cluster marker


			
        }
         
        initmapdefault = function(){
	        	 $.get("{{url($main_path)}}/get-data-map-default", function(respon){
	        		generate_map(respon.centroid, respon.points, respon.bound);
	        		update_custom_element();
	        	});
        }
        //alert(3);
        initmapdefault();

        $("#btn-cari").on('click', function(){
        		//{id_kecamatan}/{tahun}/{id_tingkat_kerusakan}
        		_id_kecamatan = $("#form-cari #id_kecamatan").val();
        		_tahun = $("#form-cari #tahun").val();
        		_id_tingkat_kerusakan = $("#form-cari #id_tingkat_kerusakan").val();
        	  	$.get("{{url($main_path)}}/get-data-map-search/"+_id_kecamatan+'/'+_tahun+'/'+_id_tingkat_kerusakan, 
        	  		function(respon){
        	  			if(respon.status=true){
        	  				generate_map(respon.data.centroid, respon.data.points, respon.data.bound);
        	  			}else{
        	  				initmapdefault();
        	  			}
		        		
		        	});
        })

	})
</script>

@endsection

	