@extends('layouts.app')

@section('content')

	<div class="col-lg-10 col-md-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>All Inventories</h2>
				@if (session('success'))
				    <div class="alert alert-success mb-3">
				        {{ session('success') }}
				    </div>
				@endif
			</div>
			<div class="wc-content">
				<table id="wc-table" class="display">
					<thead>
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Name</th>
					      <th scope="col">Email</th>
					      <th scope="col">Actions</th>
					    </tr>
					</thead>
					<tbody>
					  	@empty(!$inventories)
					  		@foreach ($inventories as $index => $inventory)
							    <tr>
							    	<td>{{ ($index + 1)}}</td>
							    	<td>{{$inventory->barcode}}</td>
							    	<td>{{$inventory->quantity}}</td>
							    	<td>
							    		<a href="{{route('edit_user', ['id' => $inventory->id]) }}" class="mr-3"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>
							    		<a class="deleteIt" href="{{route('deleteuser', ['id' => $inventory->id]) }}"><i class="fas fa-trash-alt mr-2"></i>Delete</a>							    
							    	</td>
							    </tr>
							@endforeach
					    @else
					    	<td colspan="4">No user found</td>	
					    @endif
					    
					</tbody>
				</table>
			</div>
		</div>
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
