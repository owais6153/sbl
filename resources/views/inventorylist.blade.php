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
					      <th scope="col">Locations (QTY)</th>
					      <th scope="col">Total Inventory</th>
					      <th scope="col">Moves</th>
					    </tr>
					</thead>
					<tbody>
					  	@empty(!$inventories)
					  		@foreach ($inventories['data'] as $index => $inventory)
							    <tr>
							    	<td>{{ ($index + 1)}}</td>
							    	<td>{{$inventory['barcode']}}</td>
							    	<td>
							    		@foreach ($inventory['locations'] as $location)
							    			{{$location['location_name']}} ({{$location['location_sum']}})
							    		@endforeach
							    	</td>
							    	<td>{{$inventory['total']}}</td>
							    	<td>
							    		<a href="{{route('getInventoryDetailsView', ['id' => $inventory['barcode']] ) }}">Click for all moves</a><br>
							    	</td>
							    </tr>
							@endforeach
					    @else
					    	<td colspan="4">No user found</td>	
					    @endif
					    
					</tbody>
				</table>
				{{$inventories['links']}}
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
			    $('#wc-table').DataTable({  "paging":   false,"info":     false});
			}
		} );

	</script>
@endsection
