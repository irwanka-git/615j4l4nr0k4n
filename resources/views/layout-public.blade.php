<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Bootstrap 5 Admin &amp; Dashboard Template">
	<meta name="author" content="Bootlab">
	<title>{{$pagetitle}} | {{ env('NAMA_APLIKASI') }}</title>

	<link rel="shortcut icon" href="img/favicon.ico">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&amp;display=swap" rel="stylesheet">
	<link class="js-stylesheet" href="{{url('app/css/dark.css')}}" rel="stylesheet">
	<link class="stylesheet" href="{{url('app/css/light.css')}}" rel="stylesheet">
	<link class="stylesheet" href="{{url('css/custom.css?v=2022180702')}}" rel="stylesheet">
	<link class="stylesheet" href="{{url('vendor/selectize/selectize.css')}}" rel="stylesheet">
	<link class="stylesheet" href="{{url('vendor/selectize/selectize.bootstrap5.css')}}" rel="stylesheet">
	<link class="stylesheet" href="{{url('vendor/jquery-confirm.min.css')}}" rel="stylesheet">
	<link class="stylesheet" href="{{url('vendor/lineawesome/css/line-awesome.min.css')}}" rel="stylesheet">	
	<link class="stylesheet" href="{{url('vendor/fa/css/all.min.css')}}" rel="stylesheet">	
	<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	@section("css") @show
	<!-- END SETTINGS -->
</head>

<body data-theme="dark" data-layout="fluid" data-sidebar-position="left" data-sidebar-behavior="compact">
	<div class="wrapper">
		<nav id="sidebar" class="sidebar">
			<div class="sidebar-content js-simplebar">
				<a class="sidebar-brand" href="{{url('/')}}">
          <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="20px" height="20px" viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve">
            <path d="M19.4,4.1l-9-4C10.1,0,9.9,0,9.6,0.1l-9,4C0.2,4.2,0,4.6,0,5s0.2,0.8,0.6,0.9l9,4C9.7,10,9.9,10,10,10s0.3,0,0.4-0.1l9-4
              C19.8,5.8,20,5.4,20,5S19.8,4.2,19.4,4.1z"/>
            <path d="M10,15c-0.1,0-0.3,0-0.4-0.1l-9-4c-0.5-0.2-0.7-0.8-0.5-1.3c0.2-0.5,0.8-0.7,1.3-0.5l8.6,3.8l8.6-3.8c0.5-0.2,1.1,0,1.3,0.5
              c0.2,0.5,0,1.1-0.5,1.3l-9,4C10.3,15,10.1,15,10,15z"/>
            <path d="M10,20c-0.1,0-0.3,0-0.4-0.1l-9-4c-0.5-0.2-0.7-0.8-0.5-1.3c0.2-0.5,0.8-0.7,1.3-0.5l8.6,3.8l8.6-3.8c0.5-0.2,1.1,0,1.3,0.5
              c0.2,0.5,0,1.1-0.5,1.3l-9,4C10.3,20,10.1,20,10,20z"/>
          </svg>
    
          <span class="align-middle me-3">GIS Kerusakan Ruas Jalan</span>
        </a>

				<ul class="sidebar-nav">
					<li class="sidebar-header">
						Menu
					</li>		
					<li class="sidebar-item">
						<a href="{{url('/')}}" class="sidebar-link">
			          <i class="align-middle" data-feather="home"></i> <span class="align-middle">Dashboards</span>
			      </a>
					</li>	
					

					<li class="sidebar-item">
				      <a data-bs-target="#mnu" data-bs-toggle="collapse" class="sidebar-link collapsed">
				        <i class="align-middle" data-feather="grid"></i> <span class="align-middle">Menu</span>
				      </a>
				    	<ul id="mnu" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
					      <li class="sidebar-item"><a href="{{url('titik-kerusakan-public')}}" class="sidebar-link">Sebaran Titik Kerusakan</a></li>
					      <li class="sidebar-item"><a href="{{url('laporan-kerusakan-public')}}" class="sidebar-link">Laporan Warga</a></li>
					   	</ul>
				  </li>

				  <li class="sidebar-item">
				  	<a data-bs-target="#login" data-bs-toggle="collapse" class="sidebar-link collapsed">
				        <i class="align-middle" data-feather="log-in"></i> <span class="align-middle">Login</span>
				      </a>
				      <ul id="login" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
					      <li class="sidebar-item"><a href="{{url('login')}}" class="sidebar-link">Login Admin</a></li>
					   	</ul>
				  </li>

				</ul>
			</div>
		</nav>
		<div class="main">
			<nav class="navbar navbar-expand navbar-light navbar-bg">
          <a class="sidebar-toggle">
            <i class="hamburger align-self-center"></i>
          </a>
          <b>{{ env('NAMA_APLIKASI') }}</b>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
						 
					</ul>
				</div>
			</nav>

			<main class="content" style="padding: 0px !important;">
				<div class="container-fluid p-0">
						@section('content')
						@show					  
				</div>
			</main>

			<footer class="footer">
				@include("footer")
			</footer>
		</div>
	</div>
	@section('modal')
	@show
	<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
	<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
	<script src="{{url('app/js/app.js')}}"></script>
	<script src="{{asset('vendor/jquery.form.min.js')}}"></script>
	<script src="{{asset('vendor/jquery.validate.min.js')}}"></script>
	<script src="{{asset('vendor/sweetalert.min.js')}}"></script>
	<script src="{{asset('vendor/jquery.mask.min.js')}}"></script>
	<script src="{{asset('vendor/loadingoverlay.min.js')}}"></script>
	<script src="{{url('vendor/selectize/selectize.min.js')}}"></script>
	<script src="{{url('vendor/jquery-confirm.min.js')}}"></script>
	<script src="{{url('plugin/inputmask/jquery.inputmask.min.js')}}"></script>
	<script src="{{url('plugin/inputmask/jquery.inputmask.decimal.js')}}"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script src="{{url('js/init.js?v=20220718')}}"></script>
	@section('js')
	@show
</body>
</html>