<div class="col-lg-2">
	<div class="wrap-aside">
		<div class="wa-logo">
			<img src="{{ asset('images/logo.png') }}" alt="Logo">
		</div>
		<div class="wa-menu">
			<ul class="wa-nav">
				<li class="wa-item wa-sub"><a href="{{route('user_list')}}"><i class="fas fa-users mr-3"></i>Users</a>
					<ul class="wa-sub-nav">
						<li class="wa-item"><a href="{{route('user_list')}}"><i class="fas fa-user mr-3"></i>All Users</a></li>
						<li class="wa-item"><a href="{{route('add_user')}}" ><i class="fas fa-user-plus mr-3"></i>Add User</a></li>
					</ul>
				</li>
				<li class="wa-item">
					<a href="{{route('logout')}}"><i class="fas fa-sign-out-alt mr-3"></i>Logout</a>
				</li>
			</ul>
		</div>
	</div>
</div>