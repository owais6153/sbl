@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-8">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>All Items</h2>
				@if (session('success'))
				    <div class="alert alert-success mb-3">
				        {{ session('success') }}
				    </div>
				@endif
			</div>
			<div class="wc-content">
				<div>
					@can('importnolocation')
					<a class="btn btn-primary text-light" href="{{route('addtonolocation')}}">Add Ridgefield inventory to NoLocation</a>
					@endcan
					@can('removenolocation')
					<a class="btn btn-primary text-light" href="{{route('removefromnolocation')}}">Remove All Ridgefield from NoLocation</a>
					@endcan
				</div>
				<table id="wc-table" class="display">
					<thead>
					    <tr>
					      <th scope="col">ID</th>
					      <th scope="col">Item Number</th>
					      <th scope="col">Product Identifier</th>
					    </tr>
					</thead>
				</table>
			</div>
		</div>
	</div>


@endsection

@section('script')
	<script type="text/javascript">
		$(document).ready( function () {
		   $.ajaxSetup({
		      headers: {
		          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		      }
		  });
		 
		  $('#wc-table').DataTable({
		         processing: true,
		         serverSide: true,
		         ajax: {
		          url: "{{ route('getItems') }}",
		          type: 'GET',
		         },
		         columns: [
		                  { data: 'id', name: 'id', 'visible': false},
		                  { data: 'item_number', name: 'item_number' },
		                  { data: 'productIdentifier', name: 'item_identifiers.productIdentifier' },
		               ],
		        order: [[0, 'desc']]
		  });
		  
		});
	</script>
@endsection
