<div class="col-lg-3">
	<div class="wrap-aside">
		<div class="wa-logo">
			<img src="http://the-willis-consults.dev.geeksroot.net/wp-content/uploads/2021/09/logo.svg">
		</div>
		<div class="wa-menu">
			<ul class="wa-nav">
				<li class="wa-item wa-sub open far fa-paper-plane"><a href="{{route('user_list')}}">Users</a>
					<ul class="wa-sub-nav">
						<li class="wa-item far fa-paper-plane"><a href="{{route('user_list')}}">All Users</a></li>
						<li class="wa-item far fa-paper-plane"><a href="{{route('add_user')}}" >Add User</a></li>
					</ul>
				</li>
				<li class="wa-item far fa-paper-plane">
					<a href="{{route('logout')}}">Logout</a>
				</li>
			</ul>
		</div>
	</div>
</div>