@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-9">
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
					      <th scope="col">Barcode</th>
					      <th scope="col">Quantity</th>
					      <th scope="col">From Location</th>
					      <th scope="col">To Location</th>
					      <th scope="col">Expiration Date</th>
					      <th scope="col">Pallet Numbers</th>
					      <th scope="col">Images</th>
					    </tr>
					</thead>
					<tbody>
					  	@empty(!$inventories)
					  		@foreach ($inventories as $index => $inventory)
							    <tr>
							    	<td>{{ ($index + 1)}}</td>
							    	<td>{{$inventory->barcode}}</td>
							    	<td>{{$inventory->quantity}}</td>
							    	<td>{{$inventory->from}}</td>
							    	<td>{{$inventory->to}}</td>
							    	<td>{{$inventory->expiration_date}}</td>
							    	<td>{{$inventory->pallet_number}}</td>
							    	<td>
							    		@php
							    			$images = (!empty($inventory->images)) ? explode(',', $inventory->images) : array();
							    		@endphp
							    		@foreach ($images as $image)
							    			<a target="_blank" href="{{asset('uploads/' . $image)}}">View Image</a><br>
							    		@endforeach
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
		$(document).ready( function () {
			if ( $('#wc-table').length > 0) {
			    $('#wc-table').DataTable();
			}
		} );

	</script>
@endsection
