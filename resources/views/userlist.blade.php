@extends('layouts.app')

@section('content')
	<div class="container">		
		<a href="{{route('add_user')}}" class="btn btn-info mb-4">Create User</a>

		<a href="{{route('logout')}}" class="btn btn-danger mb-4 ml-3">Logout</a>
		@if (session('success'))
		    <div class="alert alert-success mb-3">
		        {{ session('success') }}
		    </div>
		@endif
		<table class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col">#</th>
		      <th scope="col">Name</th>
		      <th scope="col">Email</th>
		      <th scope="col">Actions</th>
		    </tr>
		  </thead>
		  <tbody>
		  	@empty(!$users)
		  		@foreach ($users as $index => $user)
				    <tr>
				    	<td>{{ ($index + 1)}}</td>
				    	<td>{{$user->name}}</td>
				    	<td>{{$user->email}}</td>
				    	<td>
				    		@if ($user->id == 1 || Session::get('id') == $user->id)
				    			No actions
				    		@else 
					    		<a href="{{route('edit_user', ['id' => $user->id]) }}">Edit</a>
					    		<a class="deleteIt" href="{{route('deleteuser', ['id' => $user->id]) }}">Delete</a>
				    		@endif
				    	</td>
				    </tr>
				@endforeach
		    @else
		    	<td colspan="4">No user found</td>	
		    @endif
		  </tbody>
		</table>
	</div>
@endsection

@section('script')
	<script type="text/javascript">
		$('.deleteIt').on('click', function(e){
			e.preventDefault();
			let result = confirm('Are you sure you want to delete?');
			if (result) {
				window.location.href = $(this).attr('href');
			}
		})
	</script>
@endsection
