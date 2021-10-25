@extends('layouts.app')

@section('content')
	<div class="container">
		<form class="row" action="{{route('edituser')}}" method="POST">
			@csrf
			<div class="col-md-6 mx-auto">
				<h1>Edit Users</h1>
				    @if (Session::get('danger')) 
				    	@foreach (Session::get('danger')[0] as $error)
				    		<div class="alert alert-danger">{{$error}}</div>
				    	@endforeach
				    @endif
				<div class="form-group">
				    <label for="username">Name</label>
				    <input type="text" class="form-control" id="username" name="name" placeholder="Enter Name" value="{{$user->name}}">		   
					<input type="hidden" name="id" value="{{$user->id}}">
				  </div>
				  <div class="form-group">
				    <label for="email">Email address</label>
				    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter Email" name="email" value="{{$user->email}}">
				  </div>
				  <div class="form-group">
				    <label for="exampleInputPassword1">Password</label>
				    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password">
				  </div>
				  <button type="submit" class="btn btn-primary">Submit</button>
			</div>
		</form>
	</div>
@endsection