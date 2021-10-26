@extends('layouts.app')

@section('content')

	<div class="col-lg-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>Add User</h2>
			</div>
			@if (Session::get('danger')) 
		    	@foreach (Session::get('danger')[0] as $error)
		    		<div class="alert alert-danger">{{$error}}</div>
		    	@endforeach
		    @endif
			<div class="wc-content">
				<form action="{{route('addusers')}}" method="POST">
					   @csrf
					  <div class="form-group">
					    <label for="username">Name</label>
					    <input type="text" class="form-control" id="username" name="name" placeholder="Enter Name">		   
					
					  </div>
					  <div class="form-group">
					    <label for="email">Email address</label>
					    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter Email" name="email">
					  </div>
					  <div class="form-group">
					    <label for="exampleInputPassword1">Password</label>
					    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password">
					  </div>
					  <button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		</div>
	</div>

@endsection