<!DOCTYPE html>
<html>
<head>
	<title>Admin Pannel</title>
	<meta name="csrf-token" content="{{ csrf_token() }}">	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!-- Bootstrap Css -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<!-- Font awosme -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
	<!-- Table Css -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
	<!-- Jquery UI CSS-->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
    <!-- Select 2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom Css -->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" ></style>

	<!-- Jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<!-- Bootstrap Js -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<!-- Table Js -->
	<script type='text/javascript' src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<!-- Jquery UI JS -->
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
    <!-- Select 2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	@yield('headermeta')
</head>
<body>
	<main>
		<div class="db-sec">
			<div class="container-fluid">
				<button id="btn-toggle" class="btn-toggle" type="button">
			 		<span class="toggle-icon"><img src="{{ asset('images/toggle-icon.png') }}"></span>
			 		<span class="cross-icon"><img src="{{ asset('images/cross-icon.png') }}"></span>
			 	</button>
				<div class="row db-sec-wrap">
					@include('layouts/sidebar')
					@yield('content')
				</div>
			</div>
		</div>

	</main>
@yield('script')

<script type="text/javascript">
	$(document).ready(function(){


		$('#btn-toggle').on('click',function() {
			$('.db-sec-wrap').toggleClass('show_sidebar');
		});

		$( function() {
		    $( "#expiration_date" ).datepicker({
		    	 dateFormat: "yy-mm-dd"
		    });
	  	} );

	  	var height = $(window).height();

	});
</script>

</body>
</html>