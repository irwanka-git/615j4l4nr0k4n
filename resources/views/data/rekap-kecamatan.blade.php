<?php
$main_path = Request::segment(1);
use App\Library\Access;
?>
@extends('layout')
<style type="text/css">
	.highcharts-figure,
.highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

#container {
    height: 400px;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}

</style>
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
						 <div class="row">
						 		<div class="col-md-4">
						 			<table  class="table table-striped table-hover table-sm" style="width:100%">
										<thead>
											<tr>
												<th width="5%">#</th>
												<th width="10%">Kode</th>
												<th >Kecamatan</th>
												<th width="15%">Rusak Ringan</th>
												<th width="15%">Rusak Sedang</th>
												<th width="15%">Rusak Berat</th>
											</tr>
										</thead>
										<tbody>
											<?php $no=1;?>
											 @foreach ($data as $r)
											 <tr>
											 	<td><small>{{ $no++ }}</small></td>
											 	<td><small>{{ $r->kode_kecamatan }}</small></td>
											 	<td><small>{{ $r->nama_kecamatan }}</small></td>
											 	<td class="text-center"><small>{{ (int) $r->rusak_ringan }}</small></td>
											 	<td class="text-center"><small>{{ (int) $r->rusak_sedang }}</small></td>
											 	<td class="text-center"><small>{{ (int) $r->rusak_berat }}</small></td>
											 </tr>
											 @endforeach
										</tbody>
									</table>
						 		</div>

						 		<div class="col-md-8">
						 			<figure class="highcharts-figure">
									    <div id="container"></div>
									</figure>
						 		</div>
						 </div>

					</div>
				</div>
			</div>
		</div>
 
@endsection

@section("modal")
 

@endsection

@section("js")
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script type="text/javascript">
	$(function(){
		 

		var $tabel1 = $('#datatable').DataTable();			 
		Highcharts.chart('container', {
		    chart: {
		        type: 'column'
		    },
		    title: {
		        text: 'Rekapitulasi Kecamatan'
		    },
		    subtitle: {
		        text: 'Data Sebaran Jumlah Titik Kerusakan'
		    },
		    xAxis: {
		        categories: [
		            @foreach($data as $r)
		            '{{$r->nama_kecamatan}}',
		            @endforeach
		        ],
		        crosshair: true
		    },
		    yAxis: {
		        min: 0,
		        title: {
		            text: 'Rainfall (mm)'
		        }
		    },
		    tooltip: {
		        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
		        pointFormat: '<tr><td style="color:{series.color};padding:0;font-size:8px;">{series.name}: </td>' +
		            '<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
		        footerFormat: '</table>',
		        shared: true,
		        useHTML: true
		    },
		    plotOptions: {
		        column: {
		            pointPadding: 0.2,
		            borderWidth: 0
		        }
		    },
		    series: [{
		        name: 'Rusak Ringan',
		        data: [
		        	@foreach($data as $r)
		            {{(int)$r->rusak_ringan}},
		            @endforeach
		        	],
		        color: "#9E9E9E",
		        

		    }, {
		        name: 'Rusak Sedang',
		        data: [
		        	@foreach($data as $r)
		            {{(int)$r->rusak_sedang}},
		            @endforeach
		            ],
		        color: "#FCA100",
		        

		    }, {
		        name: 'Rusak Berat',
		        data: [
		        	@foreach($data as $r)
		            {{(int)$r->rusak_berat}},
		            @endforeach],
		        color: "#F42A06",

		    }]
		});
	})
</script>
@endsection