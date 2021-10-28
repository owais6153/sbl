@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>Import File</h2>

			</div>
				@if (Session::get('danger')) 
			    	@foreach (Session::get('danger')[0] as $error)
			    		<div class="alert alert-danger">{{$error}}</div>
			    	@endforeach
			    @endif
				@if (session('success'))
				    <div class="alert alert-success mb-3">
				        {{ session('success') }}
				    </div>
				@endif
			<div class="wc-content">
				<form action="{{route('saveImportFiles')}}" method="POST" enctype="multipart/form-data">
					   @csrf
					  <div class="form-group">
					    <label for="type">File Type</label><br>
					    <label for="on_hand">On Hand
						    <input type="radio"  id="on_hand" name="type" value="on_hand"></label>
					    <label for="on_reciving">On Reciving
						    <input type="radio"  id="on_reciving" name="type" value="on_reciving">		   
						</label>
					  </div>
					  <div class="form-group">
					    <label for="email">Upload Files</label>
					    <input type="file" class="form-control" id="file_upload" name="file_upload">
					  </div>

					  <button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		</div>
	</div>

@endsection