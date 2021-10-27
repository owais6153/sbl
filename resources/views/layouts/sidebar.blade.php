
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<div class="col-lg-2 col-md-3">
	<div class="wrap-aside">
		<div class="wa-logo">
			<img src="{{ asset('images/logo.png') }}" style="filter: invert(1);" alt="Logo">
		</div>
		<div class="wa-menu">
			<nav class="navbar navbar-inverse">
			    <div class="nav-header">
			      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>                        
			      </button>
			    </div>
			    <div class="collapse navbar-collapse" id="myNavbar">
			      <ul class="nav navbar-nav">


			        <li class="dropdown active">
			          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fas fa-users"></i>Users</a>
			          <ul class="dropdown-menu">
			            <li><a href="{{route('user_list')}}"><i class="fas fa-user"></i>All Users</a></li>
			            <li><a href="{{route('add_user')}}"><i class="fas fa-user-plus"></i>Add User</a></li>
			          </ul>
			        </li>


			        <li class="dropdown">
			        <a class="dropdown-toggle" data-toggle="dropdown" href="{{route('user_list')}}"><i class="fas fa-users"></i>Inventory Aging Reporrt</a>
					<ul class="dropdown-menu">
						<li><a href="{{route('user_list')}}"><i class="fas fa-user"></i>List All on Hands</a></li>
						<li><a href="{{route('add_user')}}" ><i class="fas fa-user-plus"></i>List All on Receive</a></li>
						<li><a href="{{route('import_files')}}" ><i class="fas fa-user-plus"></i>Import (browse, radio on hands/receive)</a></li>
					</ul>
				</li>

				<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="{{route('user_list')}}"><i class="fas fa-users"></i>Inventory Location Tracking</a>
					<ul class="dropdown-menu">
						<li><a href="{{route('addInventory')}}"><i class="fas fa-user"></i>Add Record</a></li>
						<li><a href="{{route('inventory')}}" ><i class="fas fa-user-plus"></i>List All</a></li>
					</ul>
				</li>


			        <li><a href="{{route('logout')}}"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
			        
			      </ul>
			    </div>
			</nav>
		</div>
	</div>
</div>