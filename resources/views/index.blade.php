<!DOCTYPE html>
<html>
    
<head>
	<title>My Awesome Login Page</title>
	<!-- Bootstrap Css -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<!-- Font awosme -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
	<!-- Custom Css -->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" ></style>
	<!-- Jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<!-- Bootstrap Js -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<!--Coded with love by Mutiullah Samim-->
<body>
	<section class="home-user">
		<div class="container  login-container">
			<div class="d-flex justify-content-center h-100">
				<div class="user_card">
					<div class="d-flex justify-content-center">
						<div class="brand_logo_container">
							<img src="{{ asset('images/logo.png') }}" alt="Logo">
						</div>
					</div>
					<div class="d-flex justify-content-center form_container">

						<form action="{{route('admin_login')}}" method="POST">
							@if (Session::get('danger')) 
						    	@foreach (Session::get('danger') as $error)
						    		<div class="alert alert-danger">{{$error}}</div>
						    	@endforeach
						    @endif
							@csrf
							<div class="input-group mb-3">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-user"></i></span>
								</div>
								<input type="text" name="email" class="form-control input_user" value="" placeholder="User Name">
							</div>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-key"></i></span>
								</div>
								<input type="password" name="password" class="form-control input_pass" value="" placeholder="Password">
							</div>
			
								<div class="d-flex justify-content-center mt-3 login_container">
					 	<button type="submit" name="button" class="btn btn-block login_btn">Login</button>
					   </div>
						</form>
					</div>
			

				</div>
			</div>
		</div>
	</section>
</body>
</html>