
 <div class="col-lg-3 col-md-4">
 	
	<div class="wrap-aside">
		
		<div class="wa-logo">
			<img src="{{ asset('images/logo.png') }}" style="filter: invert(1); max-width:200px; height: unset;" alt="Logo">
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
					  @if (Bouncer::can('view_all_users') || Bouncer::can('user_add') )
			        <li class="dropdown {{ (request()->is('users/*') || request()->is('users')) ? 'active' : '' }}">
			          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fas fa-users"></i>Users</a>
			          <ul class="dropdown-menu">
						 	@can('view_all_users')
			            	<li><a href="{{route('user_list')}}"><i class="fas fa-user"></i>All Users</a></li>
							@endcan
							@can('user_add')
			           			 <li><a href="{{route('add_user')}}"><i class="fas fa-user-plus"></i>Add User</a></li>
							@endcan
			          </ul>
			        </li>
					@endif

					@if (Bouncer::can('view_all_role') || Bouncer::can('role_add') )
					<li class="dropdown {{ (request()->is('roles/*') || request()->is('roles')) ? 'active' : '' }}">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fas fa-users"></i>Roles</a>
						<ul class="dropdown-menu">
							@can('view_all_role')
						 	 <li><a href="{{route('role_list')}}"><i class="fas fa-user"></i>All Roles</a></li>
							@endcan
							@can('role_add')
							  <li><a href="{{route('add_role')}}"><i class="fas fa-user-plus"></i>Add Role</a></li>
							@endcan
							</ul>
					  </li>
					  @endif
					  
					  @can('replen_batches')
					<li class=" {{ (request()->is('replen-data/*') || request()->is('replen-detail/*')) ? 'active' : '' }}">
			          <a href="{{route('replenBatch')}}"><i class="fas fa-users"></i>Replen Batches</a>
			        </li>
					@endcan
					@if (Bouncer::can('view_all_item') || Bouncer::can('item_skip') )

			        <li class="dropdown {{ ( request()->is('items') ) ? 'active' : '' }}">
			          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fas fa-box-open"></i>Items</a>
			          <ul class="dropdown-menu">
						@can('view_all_item')
			            	<li><a href="{{route('listitems')}}"><i class="fas fa-user"></i>Items</a></li>
						@endcan
						@can('item_skip')
							<li><a href="{{route('listSkippedItems')}}"><i class="fas fa-user-plus"></i>Skipped Items</a></li>
						@endcan
			          
					</ul>
			        </li>		
					@endif
					@if (Bouncer::can('inventory_view_on_hand') || Bouncer::can('inventory_view_on_receive') || Bouncer::can('import_files')  )

			        <li class="dropdown {{ (request()->is('files/*') || request()->is('files')) ? 'active' : '' }}">
			        <a class="dropdown-toggle" data-toggle="dropdown" href="{{route('user_list')}}"><i class="fas fa-users"></i>Inventory Aging Report</a>
					<ul class="dropdown-menu">
						@can('inventory_view_on_hand')	
							<li><a href="{{route('inventoryOnhand')}}"><i class="fas fa-user"></i>List All on Hands</a></li>
						@endcan
						@can('inventory_view_on_receive')
							<li><a href="{{route('inventoryOnRecive')}}" ><i class="fas fa-user-plus"></i>List All on Receive</a></li>
						@endcan
						@can('inventory_import')
							<li><a href="{{route('import_files')}}" ><i class="fas fa-user-plus"></i>Import (browse, radio on hands/receive)</a></li>
						@endcan
					
					</ul>
				</li>
				@endif
				@if (Bouncer::can('scan_inventroy') || Bouncer::can('inventory_location') )

				<li class="dropdown {{ (request()->is('inventory/*') || request()->is('inventory')) ? 'active' : '' }}">
				<a class="dropdown-toggle" data-toggle="dropdown" href="{{route('user_list')}}"><i class="fas fa-users"></i>Inventory Location Tracking</a>
					<ul class="dropdown-menu">
						@can('scan_inventroy')
							<li><a href="{{route('addInventory')}}"><i class="fas fa-user"></i>Scan Inventory</a></li>						
						@endcan
						
						@can('inventory_location')
							<li><a href="{{route('inventory')}}" ><i class="fas fa-user-plus"></i>Location</a></li>
						@endcan
					
					</ul>
				</li>

@endif
			        <li><a href="{{route('logout')}}"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
			        
			      </ul>
			    </div>
			</nav>
		</div>
	</div>
</div>