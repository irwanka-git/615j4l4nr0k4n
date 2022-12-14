<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Bootstrap 5 Admin &amp; Dashboard Template">
	<meta name="author" content="Bootlab">
	<title>Login | {{ env('NAMA_APLIKASI') }}</title>
	<link rel="canonical" href="pages-sign-in.html" />
	<link rel="shortcut icon" href="img/favicon.ico">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&amp;display=swap" rel="stylesheet">
	<link class="stylesheet" href="{{url('app/css/light.css')}}" rel="stylesheet">
 </head>
 <style type="text/css">
 	body {
	  background: url('{{url("img/background.png")}}');
	  background-repeat: no-repeat;
	  background-size: cover;
	}
 </style>

<body data-theme="default">
	<div class="main d-flex justify-content-center">
		<main class="content d-flex p-0">
			<div class="container d-flex flex-column">
				<div class="row h-100">
					<div class="col-sm-4 col-md-4 col-lg-4 mx-auto d-table h-100">
						<div class="d-table-cell align-middle">
 
							<div class="card">
								<div class="card-body">
									<div class="m-sm-4">
										<div class="text-center">
                      <img src="{{url('/img/logo.png')}}" alt="Chris Wood" class="img-fluid rounded-circle" width="120" height="120" />
											<p class="lead mt-2">
												<b>Sistem Informasi Kerusakan Ruas Jalan di Kabupaten Rokan Hulu</b>
											</p>
										</div>
                    					<hr>
										<p>
											<center>Silahkan Login dengan User ID Anda!</center>
										</p>

										<form id="login" class="form-horizontal" method="post" 
											action="{{url('submit-login')}}" enctype="multipart/form-data">
											{{csrf_field()}}
											<div class="mb-3">
												<label class="form-label">Email</label>
												<input class="form-control form-control-lg" type="text" name="email" placeholder="Masukan username" />
											</div>
											<div class="mb-3">
												<label class="form-label">Password</label>
												<input class="form-control form-control-lg" type="password" name="password" placeholder="Masukan password" />
												<small> </small>
											</div>
											<div>
											</div>
											<div class="d-grid mt-4">
											 	<button type="submit" class="btn btn-primary">Submit</button>
											</div>
										</form>
                    <hr>
                    &copy; 2022 - Reski Kurnia Putra
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="col-sm-8 col-md-8 col-lg-8 mx-auto d-table h-100">
						<div class="d-table-cell align-middle">
							  
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>

	<script src="{{url('js/app.js')}}"></script>

</body>
</html>